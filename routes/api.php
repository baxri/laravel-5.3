<?php

use App\Http\Middleware\CheckTicketAuth;

    Route::get('article/{alias}', 'Admin\NewsCrudController@article');
    Route::get('message', 'Admin\NoteCrudController@notes');

    Route::get('ip', 'IpController@current');


    Route::get('stations', 'StationController@index');
    Route::get('time', 'TransactionController@time');

    Route::get('/trains/{date}/{from}/{to}/{return?}', 'TrainController@index');

    Route::post('/ticket/register/{ticket}', 'TicketController@register');
    Route::post('/ticket/confirm/{ticket}', 'TicketController@confirm');
    Route::get('/ticket/schedule/{ticket}', 'TicketController@schedule');


    /*
     * Go to checkout
     *
     * */
    Route::post('/transaction/checkout/{transaction}', 'TransactionController@checkout');

    /*
     * Checkout callback
     *
     * */
    Route::get('/transaction/finish', 'TransactionController@finish');
    Route::post('/transaction/finish', 'TransactionController@finish');

    Route::get('/transaction/{transaction}', 'TransactionController@index');

    /*
     * Routes to return tickets
     *
     * */
    Route::get('/ticket/{request_id}', 'TicketController@index');
    Route::post('/ticket/{ticket}/authenticate', 'TicketController@authenticate');
    Route::post('/ticket/{ticket}/authorize', 'TicketController@auth');
    Route::post('/ticket/{ticket}/authorized', 'TicketController@authorized');

    Route::group( ['middleware' => CheckTicketAuth::class] , function(){

        Route::post('/ticket/{ticket}/return', 'TicketController@ret');
        Route::post('/payout/{ticket}', 'PayoutController@make');


    });

    Route::get('/server', 'TransactionController@server');
    Route::get('/ip', 'TransactionController@ip');
    Route::get('/ip/{transaction}', 'TransactionController@send');


    Route::get('/passengers/by/email/{email}', 'PersonController@passengersByEmail');
    Route::get('/passengers/{person}/remove', 'PersonController@removeFromContacts');

    Route::get('/transaction/notify/{transaction}', 'TransactionController@notify');
