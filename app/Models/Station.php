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
        $stations = $api->GetTimeTableStations();

        if( !$stations ){
            throw new Exception($api->getError());
        }

        $ordering = 0;

        foreach ( $stations as $station ){

            $ordering = $ordering + 1;

            $entity = Station::firstOrNew( array(
                'value' => $station->Code
            ) );

            $entity->label = $station->value;
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
}
