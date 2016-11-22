<?php

namespace App\helpers;



abstract class Railway
{
    public static function sort( $trains, $field, $order, $datetime = false ){
        $trains = array_values($trains);

        foreach ($trains as $key => $row) {
            if( $datetime ){
                $volume[$key]  = new \DateTime($row->$field);
            }else{
                $volume[$key]  = $row->$field;
            }
        }

        array_multisort( $volume, $order, $trains );
        return $trains;
    }

}