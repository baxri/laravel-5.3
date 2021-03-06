<?php

namespace App\Models;

use App\Gateway\Payment;
use App\Gateway\Sms;
use App\helpers\Railway;
use App\Person;
use App\RaModel;
use App\Ticket;
use Backpack\CRUD\CrudTrait;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Mockery\Exception;

class Transaction extends RaModel
{
    use CrudTrait;

    protected $table = 'transactions';
    protected $primaryKey = 'id';

    protected $fillable = [
        'checkout_id', 'amount', 'commission',
        'status', 'index', 'mobile',
        'index_mobile', 'email', 'ip', 'lang',
        'request_ids', 'quantity', 'canceled', 'returned_amount'
    ];

    public static $notfinished = 1000;
    public static $cancel = -1;
    public static $pending = 0; /* alias Initialized */
    public static $process = 1; /* alias Not Payed */
    public static $hold = 2;
    public static $success = 3;
    public static $processing = 111;
    public static $reversed = 18;

    protected $payment;

    public function __construct( array $attributes = [])
    {
        $this->payment = new Payment();
        parent::__construct($attributes);
    }

    public function tickets(){
        return $this->hasMany( Ticket::class, 'transaction_id' );
    }

    public function sms(){
        return $this->hasMany( Sms::class, 'transaction_id' );
    }

    public function ipinfo(){
        return $this->hasOne( Ip::class, 'ip_key', 'ip' );
    }

    public function logs(){
        return $this->hasMany( TransactionLog::class, 'transaction_id' );
    }

    public function setAmount( $amount ){
        $this->amount = $amount;
        $this->setCommssion();
    }

    public function setCommssion(){

        $commission_type = config('railway.commission_type');
        $commission = config('railway.commission');
        $minimal_commission = config('railway.minimal_commission');

        switch ( $commission_type ){
            case 'none':
                $c = 0;
                break;
            case 'percentage':

                $c = ( $this->amount*$commission )/100;
                $minimal_commission = $minimal_commission*100;

                if( $c < $minimal_commission ){
                    $c = $minimal_commission;
                }

                break;
            case 'fixed':
                $c = $commission*100;
                break;

        }

        $this->commission = $c;
    }

    public function bayer( $bayer ){

       if( empty( $bayer['email'] ) ){
           throw new Exception('EMAIL_NOT_DEFINED');
       }

       if ( !filter_var( $bayer['email'], FILTER_VALIDATE_EMAIL ) ) {
           throw new Exception('INVALID_EMAIL_ADDRESS');
       }

       $this->email = $bayer['email'];

       $this->setMobile($bayer['index'], $bayer['mobile'] );
    }

    public function setMobile( $index, $mobile ){

        if( empty( $index ) ){
            throw new Exception('INDEX_NOT_DEFINED');
        }

        if( empty( $mobile ) ){
            throw new Exception('MOBILE_NOT_DEFINED');
        }

        $this->index = trim(str_replace(" ", "", $index));
        $this->mobile = trim(str_replace(" ", "", $mobile));
    }

    public function checkout(){

        $language = 'en';

        if( App::isLocale('ka') ){
            $language = 'ge';
        }

        $items = [];

        $item = $this->tickets[0]->amount_from_api."|".count($this->tickets[0]->persons)."|".
            $this->tickets[0]->request_id."|".
            Railway::translateStation($this->tickets[0]->source_station)."-".
            Railway::translateStation($this->tickets[0]->destination_station);

        $items[] = $item;

        if(isset( $this->tickets[1] )){
            $item = $this->tickets[1]->amount_from_api."|".count($this->tickets[1]->persons)."|".
                $this->tickets[1]->request_id."|".
                Railway::translateStation($this->tickets[1]->source_station)."-".
                Railway::translateStation($this->tickets[1]->destination_station);

            $items[] = $item;
        }

        $params = array(
            "transaction_id"  => $this->id,
            "description"     => Railway::translateStation($this->tickets[0]->source_station)."-".
                                 Railway::translateStation($this->tickets[0]->destination_station). " - " .
                                 count($this->tickets)."x",
            "success"         => config( 'railway.site_url' ).'/'.App::getLocale().'/payment/success/'.$this->id,
            "cancel"          => config( 'railway.site_url' ).'/'.App::getLocale().'/payment/cancel',
            "amount"          => $this->amount + $this->commission,
            "order_name"      => [],
            "language"        => $language,
            "items"           => $items,
        );

        $second_mark_timeout = ( config('railway.second_mark_timeout')*60 );

        foreach ( $this->tickets as $ticket ){

           if( $ticket->second_mark_count != $ticket->update_passenger_count ){
               throw new Exception('SECOND_MARK_DONT_MUTCH_TO_UPDATED_PASSENGERS');
           }

           if( ( time() - $ticket->second_mark_time ) > $second_mark_timeout )
               throw new Exception('TICKET_SESSION_TIMEOUT');

            $params['order_name'][] = $ticket->request_id;
        }

        $params['order_name'] = implode(", ", $params['order_name']);

        $this->payment->setLogKey($this->id);

        if( !$this->payment->create( $params ) ){
            throw new Exception($this->payment->getError());
        }

        $this->checkout_id = $this->payment->payment_hash;
        $this->status = Transaction::$process;
        $this->save();

        return $this;
    }

    public function redirect(){
        return [
            'redirect' => $this->payment->redirect,
        ];
    }

    public function success(){
        $this->status = Transaction::$success;
        $this->save();
    }

    public function reverse(){
        $this->status = Transaction::$reversed;
        $this->save();
    }

    public function cancel(){
        $this->status = Transaction::$cancel;
        $this->save();
    }

    public function toExport(){

        $request_ids = [];
        $quantity = 0;
        $canceled = 0;
        $returned_amount = 0;

        if( empty( $this->quantity ) ){
            foreach ($this->tickets as $ticket){
                $request_ids[] = $ticket->request_id;

                foreach ( $ticket->persons as $person ){
                    if( $person->status  == Person::$success)
                        $quantity++;

                    if( $person->status  == Person::$returned){
                        $canceled++;
                        $returned_amount += $person->returned_amount;
                    }
                }
            }

            $request_ids = implode(",", $request_ids);

        }else{
            $request_ids = $this->request_ids;
            $quantity = $this->quantity;
            $canceled = $this->canceled;
            $returned_amount = $this->returned_amount;
        }

        return [
            'request_id' => $request_ids,
            'hash_id' => $this->checkout_id,

            'quantity' => (int)$quantity,
            'canceled' => (int)$canceled,

            'amount' => (float)number_format($this->amount/100,2),
            'commission' => (float)number_format($this->commission/100,2),
            'sum' => (float)number_format(($this->amount + $this->commission)/100, 2),

            'returned' => (float)number_format($returned_amount/100,2),

            'email' => (string)$this->email,
            'mobile' => (string)$this->mobile,
            'status' => (int)$this->status,
            'updated_at' => $this->updated_at,
        ];
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'amount' => number_format($this->amount/100,2),
            'commission' => number_format($this->commission/100,2),
            'sum' => number_format(($this->commission + $this->amount)/100,2),
            'status' => $this->status,
            'mobile' => '+'.$this->index.$this->mobile,
            'email' => $this->email,
            'tickets' => $this->tickets,
        ];
    }

    public function getMobileView(){
        return "<i class='icon-large icon-gift'>".$this->mobile."</i>";
    }

    public function getAmountView(){
        return number_format( $this->amount/100, 2 );
    }

    public function getCommissionView(){
        return number_format( $this->commission/100, 2 );
    }

    public function getReturnedAmountView(){
        return number_format( $this->returned_amount/100, 2 );
    }

    public function getCurrencyView(){
        return '₾';
    }

    public function getCheckoutIdView(){
        return '<a href="https://www.unipay.com/" target="_blank">'.$this->checkout_id.'</a>';
    }

    public function getStatusView(){


        if( $this->status == self::$pending )
            return '<span class="label label-danger">Initialized</span>';

        if( $this->status == self::$cancel )
            return '<span class="label label-danger">Canceled</span>';

        if( $this->status == self::$process )
            return '<span class="label label-warning">Not Payed</span>';

        if( $this->status == self::$hold )
            return '<span class="label label-warning">Hold</span>';

        if( $this->status == self::$success )
            return '<span class="label label-success">Success</span>';

        if( $this->status == self::$reversed )
            return '<span class="label label-danger">Reversed</span>';

        return 'Preparing';
    }

    public function getLeaveTicketStatusView(){

        $statuses = [];

        if( empty( $this->tickets[0]->persons ) ){
            return 'No Ticket';
        }

        foreach ( $this->tickets[0]->persons as $person ){
            if( isset($statuses[$person->status]) ){
                $statuses[$person->status] += 1;
            }else{
                $statuses[$person->status] = 1;
            }
        }

        $html = '';

        foreach ( $statuses as $key => $status ){

            if( $key == Person::$pending)
                $html .= '<span>Initialized ('.$status.')</span>';

            if( $key == Person::$returned)
                $html .= '<span class="label label-danger">Returned ('.$status.')</span>';

            if( $key == Person::$cancel )
                $html .= '<span class="label label-danger">Canceled ('.$status.')</span>';

            if( $key == Person::$process )
                $html .= '<span class="label label-warning">Not Payed ('.$status.')</span>';

            if( $key == Person::$hold )
                $html .= '<span class="label label-warning">Hold ('.$status.')</span>';

            if( $key == Person::$success )
                $html .= '<span class="label label-success">Success ('.$status.')</span>';

            $html.= '&nbsp;';
        }

        return $html;
    }

    public function getLogIcon(){
        $url = url(config('backpack.base.route_prefix', 'admin').'/transaction-log?transaction_id='.$this->id);
        return '<a href="'.$url.'" target="_blank"><span class="fa fa-files-o"></span></a>';
    }

    public function getSMSLogIcon(){
        $url = url(config('backpack.base.route_prefix', 'admin').'/sms?transaction_id='.$this->id);
        return '<a href="'.$url.'" target="_blank"><span class="fa fa-files-o"></span></a>';
    }

    public function getIP(){
       return str_replace(",", "<br />", $this->ip);
    }

    public function getReturnTicketStatusView(){

        $statuses = [];

        if( empty($this->tickets[1]->persons) )
            return 'No Ticket';

        foreach ( $this->tickets[1]->persons as $person ){
            if( isset($statuses[$person->status]) ){
                $statuses[$person->status] += 1;
            }else{
                $statuses[$person->status] = 1;
            }
        }

        $html = '';

        foreach ( $statuses as $key => $status ){

            if( $key == Person::$pending)
                $html .= '<span>Initialized ('.$status.')</span>';

            if( $key == Person::$returned)
                $html .= '<span class="label label-danger">Returned ('.$status.')</span>';

            if( $key == Person::$cancel )
                $html .= '<span class="label label-danger">Canceled ('.$status.')</span>';

            if( $key == Person::$process )
                $html .= '<span class="label label-warning">Not Payed ('.$status.')</span>';

            if( $key == Person::$hold )
                $html .= '<span class="label label-warning">Hold ('.$status.')</span>';

            if( $key == Person::$success )
                $html .= '<span class="label label-success">Success ('.$status.')</span>';

            $html.= '&nbsp;';
        }

        return $html;
    }

    public function getRequestID(){

        $ids = [];

        foreach ( $this->tickets as $ticket ){
            $ids[] = $ticket->request_id;
        }

        return implode("<br />", $ids);
    }

    public function emailDeliveryView(){
        return $this->email_delivery ? 'Sent' : 'Not Sent';
    }

    public function smsDeliveryView(){
        return $this->sms_delivery ? 'Sent' : 'Not Sent';
    }

    public function getUpdateedAtView(){
        return date('d M H:i ', strtotime( $this->updated_at ));
    }

    public function getSumView( $amount ){
        return number_format( $amount/100, 2 ).' GEL';
    }

    public function notify( $throw_exception = false ){
        $this->notifyEmail( $throw_exception );
        $this->notifySMS( $throw_exception );
    }

    public function notifyEmail( $throw_exception = false, $debug = false ){

        $this->email_delivery = 0;
        $pdfs = [];

        foreach ($this->tickets as $ticket){

            $path = $ticket->toPdf( $download = false, $debug );

            if( !empty($path) ){
                $pdfs[] = $path;
            }
        }

        if( count( $this->tickets ) != count($pdfs) ){
            return;
        }

       $transaction = $this;

       try{

           Mail::send('emails.notify', [ 'transaction' => $transaction ], function ($m) use ( $transaction, $pdfs ) {

               $m->from(config('railway.email_from'), config('backpack.base.project_name'));

               foreach ( $pdfs as $pdf ){
                   $m->attach($pdf, []);
               }

               $m->to($transaction->email, $transaction->mobile)->subject(
                   config('backpack.base.project_name')
               );

           });

           if( count(Mail::failures()) > 0 ) {

           } else {
               $this->email_delivery = 1;
               $this->save();
           }

       }catch( \Swift_TransportException $e ){

            if( $throw_exception )
                throw new Exception('CANNOT_SEND_EMAIL_Swift_TransportException');
       }

    }

    public function notifySMS( $throw_exception = false ){

        if( $this->status != Transaction::$success ){
            //return;
        }

        $this->sms_delivery = 0;

        $text = [];

        foreach ($this->tickets as $ticket){

            if( $ticket->status != Ticket::$success ){
                //continue;
            }

            $text[] = $ticket->textForSMS();
        }

        if( empty($text) ){
            return;
        }

        $text = implode(';', $text);

        $sms = new Sms();
        $sms->setLogKey($this->id);

        $sended = $sms->send([
            'text' => $text,
            'number' => $this->index.$this->mobile,
            'merchant_order_id' => $this->id,
        ]);

        if( $sended ){
            $this->sms_delivery = 1;
            $this->save();
        }else{
            if( $throw_exception )
                throw new Exception('CANNOT_SEND_SMS');
        }
    }


    protected static function boot()
    {
        parent::boot();

        static::deleting(function($transaction) {
            foreach ($transaction->tickets as $ticket){
                $ticket->delete();
            }

            $transaction->logs()->delete();
        });

        static::creating(function ($transaction) {
            $transaction->index_mobile = $transaction->index.$transaction->mobile;
        });

        static::updating(function ($transaction) {
            $transaction->index_mobile = $transaction->index.$transaction->mobile;
        });
    }
}
