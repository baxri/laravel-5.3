<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Log extends Model
{
	use CrudTrait;
	protected $table = 'logs';
	protected $primaryKey = 'id';

    protected $fillable = [
        'ticket_id', 'op', 'arguments', 'text'
    ];

    public function transaction(){
        return $this->belongsTo( Ticket::class );
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
