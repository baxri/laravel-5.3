<?php

use App\Http\Middleware\CheckTicketAuth;

Route::get('/stations', 'StationController@index');
Route::post('/stations/refresh', 'StationController@refresh');
Route::post('/stations/clear', 'StationController@clear');

Route::get('/trains/{date}/{from}/{to}/{return?}', 'TrainController@index');

Route::post('/ticket/register/{ticket}', 'TicketController@register');
Route::post('/ticket/confirm/{ticket}', 'TicketController@confirm');

Route::get('/transaction/{transaction}', 'TransactionController@index');
Route::post('/transaction/checkout/{transaction}', 'TransactionController@checkout');
Route::post('/transaction/finish', 'TransactionController@finish');

Route::get('/ticket/{request_id}', 'TicketController@index');

Route::post('/ticket/{ticket}/authenticate', 'TicketController@authenticate');
Route::post('/ticket/{ticket}/authorize', 'TicketController@auth');

Route::post('/ticket/return/{ticket}', 'TicketController@ret')->middleware( CheckTicketAuth::class );
Route::post('/payout/{ticket}', 'PayoutController@make')->middleware( CheckTicketAuth::class );
