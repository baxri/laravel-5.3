<?php

namespace App\Models;


use App\Gateway\Api;
use App\RaModel;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Mockery\Exception;

class Station extends RaModel
{
	use CrudTrait;

	protected $table = 'stations';
	protected $primaryKey = 'id';

    protected $fillable = [
        'label', 'value', 'code', 'filtercode', 'published', 'ordering'
    ];

    public static function refresh(){

        $api = new Api();

        $stations = $api->Trains_GetMultilingualStations('ka-GE');
        $stations_en = $api->Trains_GetMultilingualStations('en-US');

        if( !$stations ){
            throw new Exception($api->getError());
        }

        $ordering = 0;

        foreach ( $stations as $key => $station ){

            $ordering = $ordering + 1;

            $entity = Station::firstOrNew( array(
                'value' => $station->Code
            ) );

            $entity->label_ka = $station->value;
            $entity->label_en = $stations_en[$key]->value;

            $entity->filtercode = $station->FilterCode;
            $entity->ordering = $ordering;

            if( !$entity->id ){
                $entity->published = 1;
            }

            $entity->save();
        }
    }

    public static function clear(){
        self::where( 'id', '>', 1 )->delete();
    }

    public function getPublishedView(){
        return $this->published == 1 ?
            '<span style="color: lightgreen;">Published</span>':
            '<span style="color: red;">UnPublished</span>';
    }

    public function toArray(){

        $label = '';

        if( isset($this->label_ka)) $label = $this->label_ka;
        if( isset($this->label_en)) $label = $this->label_en;

        return  [
            'label' => $label,
            'value' => $this->value
        ];
    }
}
