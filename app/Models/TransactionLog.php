<?php

namespace App\Models;

use App\RaModel;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class TransactionLog extends RaModel
{
	use CrudTrait;

	protected $table = 'transaction_logs';
	protected $primaryKey = 'id';

    protected $fillable = [
        'transaction_id', 'op', 'arguments', 'text'
    ];

    public function transaction(){
        return $this->belongsTo( PayoutTransaction::class, 'transaction_id' );
    }

    public function getArgumentsView(){

        $text = '<pre>';
        $text .= print_r( json_decode( $this->arguments ), true );
        $text .= '</pre>';

        return $text;
    }

    public function getTextView(){

        if( is_object( json_decode( $this->text ) ) ){
            $text = '<pre>';
            $text .= print_r( json_decode( $this->text ), true );
            $text .= '</pre>';
            return $text;
        }else{
            return $this->text;
        }
    }
}
