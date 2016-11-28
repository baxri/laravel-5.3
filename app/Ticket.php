<?php

namespace App;

use App\Gateway\Api;
use App\Models\Log;
use App\Models\Transaction;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Mockery\Exception;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Psy\Exception\ErrorException;

class Ticket extends Model
{
    use CrudTrait;

    protected $table = 'tickets';
    protected $primaryKey = 'id';

    public static $returned = -2;
    public static $cancel = -1;
    public static $pending = 0;
    public static $process = 1;
    public static $hold = 2;
    public static $success = 3;

    public static $railway_active_status = 1;

    protected $fillable = [
        'parent_id',
        'transaction_id',
        'from',
        'to',
        'leave',
        'train',
        'class',
        'rank',
        'adults',
        'childs',

        'internet_purchase_id',
        'request_id',
        'request_guid',
        'train_id',
        'tarif_adult',
        'tarif_teen',
        'tarif_all',

        'train_name',
        'train_class',
        'vagon_type',
        'vagon_class',
        'vagon_rank',
        'leave_datetime',
        'enter_datetime',

        'status',
    ];

    public function child(){
        try{
            $child = Ticket::where('parent_id', $this->id )->first();
        }catch( \Error $e ){
            $child = null;
        }

        return $child;
    }

    public function transaction(){
        return $this->belongsTo( Transaction::class );
    }

    public function persons(){
        return $this->hasMany( Person::class, 'ticket_id' );
    }

    public function logs(){
        return $this->hasMany( Log::class, 'ticket_id' );
    }

    public function secondmark( $purchase, $ischild, $place, $place_number ){

        try{
            $person = new Person([
                'ticket_id' => $this->id,
                'purchase' => $purchase,
                'ischild' => $ischild,
                'place' => $place,
                'place_number' => $place_number,
                'status' => Person::$pending,
            ]);

            $person->secondmark();

        }catch( QueryException $e ){

        }

        return $person->toArray();
    }

    public function register( $train, $class, $rank, $adult, $child ){

        if( empty( $adult ) && empty( $child ) ){
            throw new Exception('ADULTS_AND_CHILD_VALUES_ARE_EMPTY');
        }

        if( empty( $this->internet_purchase_id ) ){

            $api = new Api();
            $api->setLogKey( $this->id );

            $trains = $api->searchTrains(
                $this->leave,
                $this->from,
                $this->to,
                $train,
                $class,
                $rank,
                $adult,
                $child );

            if( empty($trains) ){
                throw new Exception('SEARCH_TRAIN_RESULT_IS_EMPTY');
            }

            $this->internet_purchase_id = $trains[0]->InternetPurchaseId;
            $this->request_id = $trains[0]->RequestId;
            $this->request_guid = $trains[0]->RequestGuid;

            $this->train_id = $trains[0]->TrainId;
            $this->train_name = $trains[0]->TrainName;
            $this->source_station = $trains[0]->SourceStationName;
            $this->destination_station = $trains[0]->DestinationStationName;

            $this->train_class = $trains[0]->TrainClassName;

            $this->vagon = $trains[0]->VagonNumber;
            $this->vagon_type = $trains[0]->VagonTypeName;
            $this->vagon_class = $trains[0]->VagonClassName;
            $this->vagon_rank = $trains[0]->VagonRankName;

            $this->tarif_adult = $trains[0]->TarifAdult;
            $this->tarif_teen = $trains[0]->TarifTeen;
            $this->tarif_all = $trains[0]->TarifAll;
            $this->leave_datetime = $trains[0]->LeavingDate;
            $this->enter_datetime = $trains[0]->EnteringDate;
            $this->train = $train;
            $this->class = $class;
            $this->rank = $rank;
            $this->adults = $adult;
            $this->childs = $child;

            $purchaseIds = explode(",", $trains[0]->PurchaseIds);
            $isChild = explode(",", $trains[0]->IsChild);
            $placeIds = explode(",", $trains[0]->PlaceIds);
            $placeNumbers = explode(",", $trains[0]->PlaceNumbers);

            /*
             * Set Second Mark Time
             *
             * */
            $this->second_mark_time = time();

            $this->save();

            for ( $i = 0; $i < sizeof($purchaseIds); $i++ ) {
                $this->secondmark( $purchaseIds[$i], $isChild[$i], $placeIds[$i], $placeNumbers[$i]  );
            }
        }

        $this->persons;
    }

    public function confirm( array $id, array $name, array $surname, array $idnumber ){

        if( !empty( $id ) ){
            foreach ( $id as $key => $value ){

                $person = $this->persons()->find( $value );

                $person->name = $name[$key];
                $person->surname = $surname[$key];
                $person->idnumber = $idnumber[$key];

                $person->updatepassenger();
            }
        }

        $this->prepareForPayment();
        $this->moneyFromApi();

        $this->save();
    }

    public function buy(){

        $api = new Api();
        $api->setLogKey($this->id);

        $result = $api->insertTransaction(
            $this->prepare_online_payment_result,
            $this->id,
            $this->amount_from_api );

        if( !isset( $result->StatusCode ) ){
            $this->status = Ticket::$hold;
        }

        if( $result->StatusCode == 0 ){

            $this->status = Ticket::$success;

            $this->persons()->update([
                'status' => Person::$success
            ]);

            $this->reason = 'OK';

        }else{
            $this->status = Ticket::$cancel;

            $this->persons()->update([
                'status' => Person::$cancel
            ]);

            $this->reason = $result->Message;
        }

        $this->save();

        return $this->status;
    }

    public function toArray()
    {
        $type = 'departure';

        if( !empty( $this->parent_id ) ){
            $type = 'return';
        }

        $persons = $this->persons()->orderBy( 'ischild', 'ASC' )->get();
        $prepared_payouts = Person::needPayout( $this->id )->get();

        return [
           'id' => $this->id,
           'request_id' => $this->request_id,
           'type' => $type,
           'date' => date('d M', strtotime( $this->leave_datetime )),
           'time' => date('H:i', strtotime( $this->leave_datetime )),
           'enter' => date('H:i d M', strtotime( $this->enter_datetime )),
           'train' => $this->train,

           //'status' => $this->status,
           //'reason' => $this->reason,
           'name' => $this->train_name,
           'source' => $this->source_station,
           'destination' => $this->destination_station,

            'vagon_class' => $this->vagon_class,
           //'get_transaction_status' => (int)$this->get_transaction_status,
           'price' => number_format( $this->amount_from_api/100, 2 ),

            /*
             * Show persons ordered by ischild
             * First show adults then children
             *
             * */
           'prepared_for_payout' => count($prepared_payouts),
           'persons' => $persons,
        ];
    }

    public function getStatusClass(){

        if( $this->status == self::$returned )
            return 'danger';

        if( $this->status == self::$pending )
            return 'warning';

        if( $this->status == self::$cancel )
            return 'danger';

        if( $this->status == self::$process )
            return 'warning';

        if( $this->status == self::$hold )
            return 'warning';

        if( $this->status == self::$success )
            return 'success';

        return $this->status;
    }

    public function getStatusName(){

        if( $this->status == self::$returned )
            return 'Returned';

        if( $this->status == self::$pending )
            return 'Pending';

        if( $this->status == self::$cancel )
            return 'Cancel';

        if( $this->status == self::$process )
            return 'Warning';

        if( $this->status == self::$hold )
            return 'Hold';

        if( $this->status == self::$success )
            return 'Success';

        return $this->status;
    }

    public function authenticate(){
        $this->action_token = strtoupper(str_random(5));
        $this->action_token_time = time();
        $this->save();

        return $this->action_token;
    }

    public function authorize( $code ){

        if( $code != $this->action_token ){
            throw new Exception('INVALID_CODE');
        }

        $this->action_token = Hash::make(str_random(32));
        $this->action_token_time = time();
        $this->save();

        return $this->action_token;
    }

    public function authorized( $token = null ){

        $ticket_action_lifetime = config('railway.ticket_action_lifetime');

        if( $token !== null && $token != $this->action_token ){
            return false;
        }

        $time = time() - $this->action_token_time;

        if( $time > $ticket_action_lifetime*60 ){
            return false;
        }

        return true;
    }

    public function toPdf( $download = false ){

        $path = config('railway.pdf_location').$this->request_id.'.pdf';

        $pdf = Pdf::loadView('pdf.ticket-pdf', [
            'ticket' => $this
        ]);

        try{

            if( $download ){
                return $pdf->stream();
            }else{
                $pdf->save($path);
            }


        }catch( ErrorException $e ){
            if( $download ){
                return $pdf->stream();
            }else{
                $pdf->save($path);
            }
        }

        return $path;
    }

    public function html( $download = false ){
        return view('pdf.ticket-pdf', [
            'ticket' => $this
        ]);
    }

    public function textForSMS(){

        if( empty( $this->parent_id ) ){
            $subject = 'Departure/Gamgzavreba - ';
        }else{
            $subject = 'Return/Dabruneba - ';
        }

        return $subject.'PurchaseID/Sekvetis Kodi: '.$this->request_id;
    }

    public function sync(){

        $api = new Api();
        $api->setLogKey($this->id);

        $places = count( $this->persons );
        $status = $api->GetTransactionStatus( $this->request_id, $places );

        //if( $this->get_transaction_status != 1 ){
            $this->get_transaction_status = (int)$status->GetTransactionStatusResult;
            $this->save();
       // }

        return [
            'railway_status' => (int)$status->GetTransactionStatusResult
        ];
    }

    public function getAmountView(){
        return number_format( $this->amount_from_api/100, 2 );
    }

    public function getUpdateedAtView(){
        return date('d M H:i ', strtotime( $this->updated_at ));
    }

    public function getCreatedAtView(){
        return date('d M H:i ', strtotime( $this->updated_at ));
    }

    public function gePersonstatusView(){

        $statuses = [];

        foreach ( $this->persons as $person ){
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

    public function getTicketType(){

        if( $this->child() ){
            return '
            <span class="fa fa fa-subway" aria-hidden="true">
            <span class="fa fa-long-arrow-right" aria-hidden="true"></span>';
        }
        else{
            return '
            <span class="fa fa fa-subway" aria-hidden="true">
            <span class="fa fa-long-arrow-left" aria-hidden="true"></span>';
        }

    }

    private function prepareForPayment(){
        $api = new Api();
        $api->setLogKey( $this->id );
        $result = $api->PrepareOnlinePayment( $this->request_guid, $this->internet_purchase_id, $this->train_id );

        if( empty( $result->PrepareOnlinePaymentResult ) ){
            throw new Exception('CANNOT_RETRIVE_PREPARE_ONLINE_PAYMENT_RESULT');
        }

        $this->prepare_online_payment_result = $result->PrepareOnlinePaymentResult;
    }

    private function moneyFromApi(){

        $api = new Api();
        $api->setLogKey($this->id);
        $result = $api->GetMoneyAmount( $this->prepare_online_payment_result );

        if( $result->GetMoneyAmountResult <= 0 ){
            throw new Exception("CANNOT_RETRIVE_AMOUNT_FROM_RAILWAY_SERVER");
        }

        if( empty( $result->GetMoneyAmountResult ) ){
            throw new Exception('CANNOT_RETRIVE_MONEY_FROM_API_SERVER');
        }

        $this->amount_from_api = $result->GetMoneyAmountResult;
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($ticket) {
            $ticket->persons()->delete();
        });
    }

}
