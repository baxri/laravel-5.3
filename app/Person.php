<?php

namespace App;

use App\Gateway\Api;
use App\Models\PayoutTransaction;
use Illuminate\Database\Eloquent\Model;
use Mockery\Exception;

class Person extends RaModel
{
    protected $table = 'persons';
    private $api;

    public static $returned = -2;
    public static $cancel = -1;
    public static $pending = 0;
    public static $process = 1;
    public static $hold = 2;
    public static $success = 3;

    protected $fillable = [
        'payout_transaction_id', 'ticket_id', 'purchase', 'ischild', 'place',
        'place_number', 'name', 'surname', 'idnumber', 'status'
    ];

    public function __construct(array $attributes = []){
        $this->api = $api = new Api();
        parent::__construct($attributes);
    }

    public function ticket(){
        return $this->belongsTo( Ticket::class );
    }

    public function payout(){
        return $this->belongsTo( PayoutTransaction::class, 'payout_transaction_id' );
    }

    public function setPayout( PayoutTransaction $payout ){
        $this->payout_transaction_id = $payout->id;
        $this->save();
    }

    public function scopeNeedPayout( $query, $ticket_id ){

        $query->leftjoin('payout_transactions', 'payout_transactions.id', '=', 'persons.payout_transaction_id');

        $query->where( 'persons.ticket_id', $ticket_id );
        $query->where( 'persons.status', Person::$returned );


        $query->where( function($query){

            $query->where('payout_transactions.status', -1 )
                ->orWhere('payout_transactions.status', NULL);
            ;

        } );


        d($query->toSql());
    }

    public function secondmark(){
        if( !$this->second_mark  ){
            $this->api->setLogKey( $this->ticket->id );
            $response = $this->api->secondMark( $this->purchase );

            if( !isset( $response->code ) || $response->code != 0 ){
                throw new Exception('SECOND_MARK_FAILED');
            }

            $this->ticket->second_mark_count++;
            $this->ticket->save();

            $this->second_mark = 1;
            $this->save();
        }
    }

    public function updatepassenger(){

        if( empty( trim($this->name) ) ){
            throw new Exception('PASSENGER_NAME_IS_EMPTY');
        }

        if( empty( trim($this->surname) ) ){
            throw new Exception('PASSENGER_SURNAME_IS_EMPTY');
        }

        if( empty( trim($this->idnumber) ) ){
            throw new Exception('IDNUMBER_NAME_IS_EMPTY');
        }

        if( !$this->update_passenger  ){

            /*
             * Update Passenger info
             *
             * */

            $this->api->setLogKey( $this->ticket->id );

            $response = $this->api->UpdatePassengers(
                $this->ticket->internet_purchase_id,
                $this->purchase,
                $this->name,
                $this->surname,
                $this->idnumber );

            if( !isset( $response->code ) || $response->code != 0 ){
                throw new Exception('UPDATE_PASSENGERS_FAILED');
            }

            /*
             * Check passenger discount
             *
             * */

            $result = $this->api->GetPriceStudentDiscountInfo( $this->purchase );

            if( !isset( $result->code ) || $result->code != 0 ){
                throw new Exception('GET_PRICE_STUDENT_DISCOUNT_INFO_FAILED');
            }

            if( isset( $result->DescountMoneyMount ) ){
                $this->discount_amount = $result->DescountMoneyMount;
            }

            if( isset( $result->Price ) ){
                $this->price = $result->Price;
            }

            if( $this->ischild ){
                $this->tarif = $this->ticket->tarif_teen;
            }else{
                $this->tarif = $this->ticket->tarif_adult;
            }

            $this->ticket->update_passenger_count++;
            $this->ticket->save();

            $this->status = Person::$process;
            $this->update_passenger = 1;
            $this->save();
        }
    }

    public function ret( $check = false ){

        //return true;

        if( $this->status != Person::$success ){
            return false;
        }

        $this->api->setLogKey($this->ticket->id);
        $result = $this->api->CancelPurchase( $this->purchase );

        if( $result->StatusCode == 0 ){
            $this->returned_amount = $result->ReturnedMoney;
            $this->status = Person::$returned;
            $this->save();

            return true;
        }

        return false;
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
            return 'Process';

        if( $this->status == self::$hold )
            return 'Hold';

        if( $this->status == self::$success )
            return 'Success';

        return $this->status;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'place' => $this->place_number,
            'ischild' => $this->ischild,
            'name' => $this->name,
            'surname' => $this->surname,
            'idnumber' => $this->idnumber,
            'status' => $this->status,
            'price' => number_format($this->tarif/100,2),
            'discount' => number_format($this->discount_amount/100,2),
            'returned_amount' => number_format($this->returned_amount/100,2),
            //'payout' => $this->payout,
        ];
    }
}
