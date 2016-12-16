<?php

namespace App;

use App\Gateway\Api;
use App\helpers\Railway;
use App\Models\Station;
use Illuminate\Database\Eloquent\Model;
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

        $api = new Api();
        $trains = $api->GetFreePlacePrices( $date, $from, $to );

        echo $date;
        d($trains);

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
                    'name'   => $item->trainname,
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
        ]);
        $ticket->save();

        $trains = array_values($categorised);

        $source = Station::where('value', $from)->get();
        $destination = Station::where('value', $to)->get();

        return array(
            'ticket' => $ticket->id,

            'source' => $source[0]->label,
            'destination' => $destination[0]->label,

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
        $leave = new \DateTime($this->date);
        $enter = new \DateTime($this->enter);
        $interval = $leave->diff($enter);

        //$this->_refilVagons();

        return [
            'number' => $this->number,
            'name' => $this->name,
            'date' =>  \date('D d M', \strtotime( $this->date )),
            'departure' =>  \date('H:i', strtotime($this->date)),
            'arrive' => \date('H:i', \strtotime( $this->enter )),
            'duration' => $interval->h.'h '.$interval->i.'m',
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
