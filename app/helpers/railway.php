<?php

namespace App\helpers;



use App\Models\Station;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

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

    public static function translateDate( $date, $hstring = "", $lang = null, $only_month = false ){

        if( $lang != null ){
            $was = App::getLocale();
            App::setLocale($lang);
        }

        $day = self::translate(strtoupper(date('l',strtotime( $date. $hstring ))));
        $d = date('d',strtotime( $date. $hstring ));
        $month = self::translate(strtoupper(date('F',strtotime( $date. $hstring ))));

        if( $lang != null ){
            App::setLocale($was);
        }

        if( $only_month ){
            return $month;
        }

        return $day.' '.$d.' '.$month;
    }

    public static function pdf( $filename, $view, $data = array(), $mergeData = array(), $download = false ){

        $html = \View::make($view, $data, $mergeData)->render();

        $mpdf = new \mPDF(
            Config::get('pdf.mode'),              // mode - default ''
            Config::get('pdf.format'),            // format - A4, for example, default ''
            Config::get('pdf.default_font_size'), // font size - default 0
            Config::get('pdf.default_font'),      // default font family
            Config::get('pdf.margin_left'),       // margin_left
            Config::get('pdf.margin_right'),      // margin right
            Config::get('pdf.margin_top'),        // margin top
            Config::get('pdf.margin_bottom'),     // margin bottom
            Config::get('pdf.margin_header'),     // margin header
            Config::get('pdf.margin_footer'),     // margin footer
            Config::get('pdf.orientation')        // L - landscape, P - portrait
        );

        $mpdf->SetDisplayMode('fullpage');
        $mpdf->list_indent_first_level = 0;  // 1 or 0 - whether to indent the first level of a list
        $mpdf->WriteHTML($html);

        if( $download ){
            return $mpdf->Output($filename, 'I');
        }else{
            $mpdf->Output($filename, 'F');
            return $filename;
        }
    }

}