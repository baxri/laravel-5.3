<?php

namespace App\Models;

use App\Gateway\Payout;
use App\helpers\PayoutValidation;
use App\PayoutInfo;
use App\Person;
use App\RaModel;
use App\Ticket;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Mockery\Exception;

class PayoutTransaction extends RaModel
{
	use CrudTrait;

	protected $table = 'payout_transactions';
	protected $primaryKey = 'id';

    protected $fillable = [
        'id', 'payout_hash_id', 'name', 'surname', 'idnumber', 'birth_date', 'iban', 'bank', 'status', 'amount', 'ip'
    ];

    public static $cancel = -1;
    public static $pending = 0;
    public static $process = 1;
    public static $hold = 2;
    public static $success = 3;
    public static $processing = 111;

    public function persons(){
        return $this->hasMany( Person::class, 'payout_transaction_id' );
    }

    public function logs(){
        return $this->hasMany( Payout_log::class, 'payout_id' );
    }

    public function ipinfo(){
        return $this->hasOne( Ip::class, 'ip_key', 'ip' );
    }

    public function make( Ticket $ticket, $options, $persons ){

       // return true;

        $this->name = $options['name'];
        $this->surname = $options['surname'];
        $this->idnumber = $options['idnumber'];
        $this->birth_date = $options['birth_date'];
        $this->iban = $options['iban'];
        $this->ip = $options['ip'];

        if( empty( $this->name ) ) throw new Exception('NAME_NOT_DEFINED');
        if( empty( $this->surname ) ) throw new Exception('SURNAME_NOT_DEFINED');
        if( empty( $this->idnumber ) ) throw new Exception('IDNUMBER_NOT_DEFINED');
        if( empty( $this->birth_date ) ) throw new Exception('BIRTH_DATE_NOT_DEFINED');
        if( empty( $this->iban ) ) throw new Exception('IBAN_NOT_DEFINED');

        if( !PayoutValidation::checkname( $this->name ) ){
            throw new Exception('NAME_FORMAT_IS_INVALID');
        }

        if( !PayoutValidation::checkname( $this->surname ) ){
            throw new Exception('SURNAME_FORMAT_IS_INVALID');
        }

        if( !PayoutValidation::checkIDnumber( $this->idnumber ) ){
            throw new Exception('IDNUMBER_FORMAT_IS_INVALID');
        }

        if( !PayoutValidation::checkBirthDate( $this->birth_date ) ){
            throw new Exception('BIRTH_DATE_FORMAT_IS_INVALID');
        }

        if( !$this->bank = PayoutValidation::checkIBan( $this->iban ) ){
            throw new Exception('IBAN_FORMAT_IS_INVALID');
        }

        $sum = 0;

        foreach ( $persons as $person ){
            $sum += $person->returned_amount;
            $this->description .= ' '.$person->purchase.' - '.$person->name.' '.$person->surname.' ('.$person->idnumber.') ';
        }

        if( empty( $sum ) ){
            throw new Exception('NOTHIG_TO_RETURN_FOR_THIS_TICKET');
        }

        $payout_fee = config('railway.payout_fee');

        $this->amount = $sum - $payout_fee;
        $this->commission = $payout_fee;

        $this->status = PayoutTransaction::$pending;
        $this->save();

        foreach ( $persons as $person ){
            $person->setPayout( $this->id );
        }

        PayoutInfo::create([

            'email' => $ticket->transaction->email,

            'index' => $ticket->transaction->index,
            'mobile' => $ticket->transaction->mobile,

            'name' => $this->name,
            'surname' => $this->surname,
            'idnumber' => $this->idnumber,
            'birth_date' => $this->birth_date,
            'iban' => $this->iban,
        ]);


        $this->send();
    }

    public function send(){

        if( $this->status == PayoutTransaction::$success || $this->status == PayoutTransaction::$cancel  ){
            throw new Exception('TRANSACTION_ALREADY_SENDED_TO_PROVIDER');
        }

        $payout = new Payout();
        $payout->setLogKey( $this->id );

        $hash_id = $payout->send([
            'MerchantUserID' => $this->name.'@'.$this->surname,
            'MerchantOrderID' => $this->id,
            'BankID' => $this->bank,
            'Amount' => number_format( ($this->amount)/100, 2 ),
            'Currency' => 'GEL',
            'Name' => $this->name,
            'Surname' => $this->surname,
            'PrivateNumber' => $this->idnumber,
            'BirthDate' => $this->birth_date,
            'Description' => $this->description,
            'Iban' => $this->iban,
        ]);

        if( $hash_id ){
            $this->status = PayoutTransaction::$success;
            $this->payout_hash_id = $hash_id;
        }else{
            $this->status = PayoutTransaction::$hold;
        }

        $this->save();
    }

    public function getAmountView(){
        return number_format( $this->amount/100, 2 );
    }

    public function getStatusView(){

        if( $this->status == self::$pending )
            return '<span class="label label-warning">Pending</span>';

        if( $this->status == self::$cancel )
            return '<span class="label label-danger">Canceled</span>';

        if( $this->status == self::$process )
            return '<span class="label label-warning">Process</span>';

        if( $this->status == self::$hold )
            return '<span class="label label-warning">Hold</span>';

        if( $this->status == self::$success )
            return '<span class="label label-success">Success</span>';

        return $this->status;
    }

    public function getLogIcon(){
        $url = url(config('backpack.base.route_prefix', 'admin').'/payout-log?payout_id='.$this->id);
        return '<a href="'.$url.'" target="_blank"><span class="fa fa-files-o"></span></a>';
    }

    public function toArray()
    {
        $return = parent::toArray();
        $return['tickets'] = $this->persons;
        return $return;
    }
}
