<?php

namespace App;

use App\Gateway\Api;
use App\helpers\Railway;
use App\Models\Station;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Mockery\Exception;

class Train extends RaModel
{
    protected $fillable = [
        'number', 'name', 'date', 'time', 'enter'
    ];

    public $vagons = [];

    public static $allclasses = [];
    public static $allvagons = [];

    public static function trains( $date, $from, $to, $transaction_id, $parent_id = 0 ){

        if( empty( $from ) ){
            throw new Exception('SOURCE_STATION_NOT_DEFINED');
        }

        if( empty( $to ) ){
            throw new Exception('DESTINATION_STATION_NOT_DEFINED');
        }

        $source = Station::where('value', $from)->get();
        $destination = Station::where('value', $to)->get();

        if( App::isLocale('en') ){
            $source = $source[0]->label_en;
            $destination = $destination[0]->label_en;
        }else{
            $source = $source[0]->label_ka;
            $destination = $destination[0]->label_ka;
        }

        $api = new Api();
        $trains = $api->GetFreePlacePrices( $date, $from, $to );

        if( empty($trains) ){
            throw new Exception('TRAINS_NOT_FOUND');
        }

        $categorised = [];

        foreach ( $trains as $key => $item ){

            $differencetime = config('railway.differencetime');

            if( $item->differenceTime < $differencetime ) {
                unset($item[$key]);
                continue;
            }

            if( !isset( $categorised[$item->TrainNumber] ) ){
                $train = new Train([
                    'number' => $item->TrainNumber,
                    //'name'   => $item->trainname,
                    'name'   => $source.'-'.$destination,
                    'date'   => $item->LeavingTime,
                    'time'   => $item->LeavingTime,
                    'enter'  => $item->EnteringTime,
                ]);
                $categorised[$item->TrainNumber] = $train;
            }else{
                $train = $categorised[$item->TrainNumber];
            }

            $categorised[$item->TrainNumber]->vagon( new Vagon([
                'train' => $train->number,
                'class' => $item->xVagonClassId,
                'rank' => $item->xVagonRankId,
                'name' => $item->VagonClassName,
                'amount' => $item->MoneyAmount,
                'enable' => 1
            ]) );
        }

        $ticket = new Ticket([
            'from' => $from,
            'to' => $to,
            'leave' => $date,
            'parent_id' => $parent_id,
            'transaction_id' => $transaction_id,
            'status' => Ticket::$pending,
            'lang' => App::getLocale(),
        ]);
        $ticket->save();

        $trains = array_values($categorised);

        return array(
            'ticket' => $ticket->id,

            'source' => $source,
            'destination' => $destination,

            'date' => $train->toArray()['date'],
            'trains' => Railway::sort( $trains, 'time', SORT_ASC, true ),
        );
    }

    public function vagon( $vagon, $refill = false ){

        Train::$allclasses[] = $vagon->class;
        Train::$allvagons[$vagon->class] = $vagon;

        if( !isset( $this->vagons[$vagon->class] ) ){

            /*
             * If refil parameter is true
             * fill vagons array with disabled vagons
             * to make all vagons array same size
             *
             * */

            if( $refill ){
                $vagon->enable = 0;
            }

            $this->vagons[$vagon->class] = $vagon;
        }
    }

    public function toArray()
    {
        return [
            'number' => $this->number,
            'name' => $this->name,
            
            'date' =>  date('D d M',strtotime( $this->date. "+4hours" )), 
            'departure' =>  date('H:i', strtotime($this->date. "+4hours")),
            'arrive' => date('H:i', strtotime( $this->enter. "+4hours" )),

            //'Tdate' =>  $this->date,
            //'Tdeparture' =>  $this->date,
            //'Tarrive' => $this->enter,
            
            'vagons' => Railway::sort( $this->vagons,
                config( 'railway.sort_vagons_field' ),
                config( 'railway.sort_vagons_order' ) ),
        ];
    }

    /*
     * makes all vagons array same dimension (size)
     *
     * */
    private function _refilVagons(){
        $classes = array_unique(Train::$allclasses);

        foreach ( $classes as $class ){
            $this->vagon( Train::$allvagons[$class], $refill = true );
        }
    }
}
