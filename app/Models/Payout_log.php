<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Payout_log extends Model
{
	use CrudTrait;

	protected $table = 'payout_logs';
	protected $primaryKey = 'id';
	// public $timestamps = false;
	// protected $guarded = ['id'];
	// protected $fillable = [];
	// protected $hidden = [];
    // protected $dates = [];

    protected $fillable = [
        'payout_id', 'arguments', 'text'
    ];

    public function payout(){
        return $this->belongsTo( PayoutTransaction::class, 'payout_id' );
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
