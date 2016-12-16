<?php

namespace App\helpers;



use App\Models\Station;
use Illuminate\Support\Facades\App;

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

    public static function trans( $symbols, $lang = null ){

        if( $lang != null ){
            $was = App::getLocale();
            App::setLocale($lang);
        }

        $trans = trans('railway.'.$symbols);

        if( $lang != null ){
            App::setLocale($was);
        }

        return $trans;
    }

    public static function translate( $symbols, $lang = null ){

        if( $lang != null ){
            $was = App::getLocale();
            App::setLocale($lang);
        }

        $geo = array( 'ა','ბ','გ','დ','ე','ვ','ზ','თ','ი','კ','ლ','მ','ნ','ო','პ','ჟ','რ','ს','ტ','უ','ფ','ქ','ღ','ყ','შ','ჩ','ც','ძ','წ','ჭ','ხ','ჯ','ჰ',' ','.','(',')','-' );
        $lat = array( 'A','B','G','D','E','V','Z','T','I','K','L','M','N','O','P','J','R','S','T','U','F','K','GH','KH','SH','CH','TS','DZ','TS','CH','KH','J','H','','','','','_' );
        $convertedSymbols = str_replace($geo, $lat, $symbols);

        $trans = trans('railway.'.$convertedSymbols);

        if( $lang != null ){
            App::setLocale($was);
        }

        return $trans;
    }

    public static function translateStation( $station, $lang = null ){

        if( $lang != null ){
            $was = App::getLocale();
            App::setLocale($lang);
        }

        try{
            $station = Station::where('label_ka', $station )->first();
        }catch( \Error $e ){

            if( $lang != null ){
                App::setLocale($was);
            }

            return $station;
        }

        if( App::isLocale('en') ){
            $translated = $station->label_en;
        }else{
            $translated = $station->label_ka;
        }

        if( $lang != null ){
            App::setLocale($was);
        }

        return $translated;
    }

}