<?php

namespace App\Models;

use App\Gateway\Payment;
use App\Gateway\Sms;
use App\helpers\Railway;
use App\Person;
use App\Ticket;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Illuminate\Support\Facades\Mail;
use Mockery\Exception;

class Transaction extends Model
{
    use CrudTrait;

    protected $table = 'transactions';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    protected $fillable = [
        'checkout_id', 'amount', 'commission', 'status', 'index', 'mobile',
        'index_mobile', 'email', 'created_at', 'updated_at', 'ip'
    ];

    public static $cancel = -1;
    public static $pending = 0;
    public static $process = 1;
    public static $hold = 2;
    public static $success = 3;
    public static $processing = 111;

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
       $this->email = $bayer['email'];
       $this->setMobile($bayer['index'], $bayer['mobile'] );
    }

    public function setMobile( $index, $mobile ){
        $this->index = $index;
        $this->mobile = $mobile;
        //$this->index_mobile = $index.$mobile;
    }

    public function checkout(){

        $params = array(
            "transaction_id"  => $this->id,
            "description"     => $this->tickets[0]->train_name . " - " . count($this->tickets)."x",
            "success"         => config( 'railway.checkout_success' ).'/'.$this->id,
            "cancel"          => config( 'railway.checkout_cancel' ).'/'.$this->id,
            "amount"          => $this->amount + $this->commission,
            "order_name"      => [],
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

    public function cancel(){
        $this->status = Transaction::$cancel;
        $this->save();
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

    public function getCurrencyView(){
        return '₾';
    }

    public function getCheckoutIdView(){
        return '<a href="https://www.unipay.com/" target="_blank">'.$this->checkout_id.'</a>';
    }

    public function getStatusView(){

        if( $this->status == self::$cancel )
            return '<span class="label label-danger">Canceled</span>';

        if( $this->status == self::$process )
            return '<span class="label label-warning">Process</span>';

        if( $this->status == self::$hold )
            return '<span class="label label-warning">Hold</span>';

        if( $this->status == self::$success )
            return '<span class="label label-success">Success</span>';

        return 'Preparing';
    }

    public function getLeaveTicketStatusView(){

        $statuses = [];

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
                $html .= '<span>Pending ('.$status.')</span>';

            if( $key == Person::$returned)
                $html .= '<span class="label label-danger">Returned ('.$status.')</span>';

            if( $key == Person::$cancel )
                $html .= '<span class="label label-danger">Canceled ('.$status.')</span>';

            if( $key == Person::$process )
                $html .= '<span class="label label-warning">Process ('.$status.')</span>';

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
                $html .= '<span>Pending ('.$status.')</span>';

            if( $key == Person::$returned)
                $html .= '<span class="label label-danger">Returned ('.$status.')</span>';

            if( $key == Person::$cancel )
                $html .= '<span class="label label-danger">Canceled ('.$status.')</span>';

            if( $key == Person::$process )
                $html .= '<span class="label label-warning">Process ('.$status.')</span>';

            if( $key == Person::$hold )
                $html .= '<span class="label label-warning">Hold ('.$status.')</span>';

            if( $key == Person::$success )
                $html .= '<span class="label label-success">Success ('.$status.')</span>';

            $html.= '&nbsp;';
        }

        return $html;
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

    public function notify(){
        $this->notifyEmail();
        $this->notifySMS();
    }

    public function notifyEmail(){

        if( $this->status != Transaction::$success ){
            //return;
        }

        $this->email_delivery = 0;
        $pdfs = [];

        foreach ($this->tickets as $ticket){

            if( $ticket->status != Ticket::$success ){
                //continue;
            }

            $path = $ticket->toPdf();

            if( !empty($path) ){
                $pdfs[] = $path;
            }
        }

        if( count( $this->tickets ) != count($pdfs) ){
            return;
        }


       $transaction = $this;

       Mail::send('emails.notify', [ 'transaction' => $transaction ], function ($m) use ( $transaction, $pdfs ) {

            $m->from(config('railway.email_from'), config('backpack.base.project_name'));

            foreach ( $pdfs as $pdf ){
                $m->attach($pdf, []);
            }

            $m->to($transaction->email, $transaction->mobile)->subject(
                strtoupper( config('backpack.base.project_name') )
            );

        });

       $this->email_delivery = 1;
       $this->save();
    }

    public function notifySMS(){

        if( $this->status != Transaction::$success ){
            //return;
        }

        $this->sms_delivery = 0;

        $text = [];

        foreach ($this->tickets as $ticket){

            if( $ticket->status != Ticket::$success ){
                continue;
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
        }

        $this->save();
    }


    protected static function boot()
    {
        parent::boot();

        static::deleting(function($transaction) {
            foreach ($transaction->tickets as $ticket){
                $ticket->delete();
            }
        });

        static::creating(function ($transaction) {
            $transaction->index_mobile = $transaction->index.$transaction->mobile;
        });

        static::updating(function ($transaction) {
            $transaction->index_mobile = $transaction->index.$transaction->mobile;
        });
    }
}
