<?php

use App\Http\Middleware\CheckTicketAuth;


Route::post('/ticket/jwt', 'TicketController@jwt');

/*
 * Routes for buy tickets
 *
 * */

Route::get('stations', 'StationController@index');
Route::get('/trains/{date}/{from}/{to}/{return?}', 'TrainController@index');

Route::post('/ticket/register/{ticket}', 'TicketController@register');
Route::post('/ticket/confirm/{ticket}', 'TicketController@confirm');



/*
 * Go to checkout
 *
 * */


Route::post('/transaction/checkout/{transaction}', 'TransactionController@checkout');

/*
 * Checkut callback
 *
 * */

Route::post('/transaction/finish', 'TransactionController@finish');
Route::get('/transaction/{transaction}', 'TransactionController@index');


/*
 * Routes to return tickets
 *
 * */

Route::get('/ticket/{request_id}', 'TicketController@index');

Route::post('/ticket/{ticket}/authenticate', 'TicketController@authenticate');
Route::post('/ticket/{ticket}/authorize', 'TicketController@auth');

Route::group(['middleware' => CheckTicketAuth::class], function(){

    Route::post('/ticket/{ticket}/return', 'TicketController@ret');
    Route::post('/payout/{ticket}', 'PayoutController@make');

});


