<?php

namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\Support\Facades\App;
use Mockery\Exception;

class StationController extends Controller
{
    public function index()
    {
        $stations = Station::all([
            'label_'.App::getLocale('en'), 'value'
        ]);

        return response()->ok( $stations->toArray() );
    }

    public function refresh()    {

        try{
            Station::refresh();

           return response()->ok();
        }catch( Exception $e ){
           return response()->error( $e->getMessage() );
        }
    }

    public function clear()
    {
        return response()->ok(
            Station::clear()
        );
    }
}
