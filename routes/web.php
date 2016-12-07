<?php

Route::get('/', function () {
    return redirect(config('railway.redirect_api_link'));
});

Route::group(['prefix' => 'raconsole', 'middleware' => 'admin'], function()
{
    Route::get('/', 'Admin\DashboardController@index');
    Route::get('/dashboard', 'Admin\DashboardController@index');

    CRUD::resource('station', 'Admin\StationCrudController');

    CRUD::resource('ip', 'Admin\IpCrudController');

    CRUD::resource('transaction', 'Admin\TransactionCrudController');
    CRUD::resource('ticket', 'Admin\TicketCrudController');
    CRUD::resource('sms', 'Admin\SmsCrudController');

    CRUD::resource('transaction-log', 'Admin\TransactionLogCrudController');
    CRUD::resource('log', 'Admin\LogCrudController');

    CRUD::resource('payout', 'Admin\PayoutTransactionCrudController');
    CRUD::resource('payout-log', 'Admin\Payout_LogCrudController');

    Route::post('person/return/{person}', 'Admin\TransactionCrudController@ret');
    Route::post('transaction/resendemail/{transaction}', 'Admin\TransactionCrudController@resendEmail');
    Route::post('transaction/resendsms/{transaction}', 'Admin\TransactionCrudController@resendSms');

    Route::get('transaction/ticket/{ticket}/pdf', 'Admin\TransactionCrudController@pdf');
    Route::get('transaction/ticket/{ticket}/html', 'Admin\TransactionCrudController@html');
    Route::post('transaction/ticket/{ticket}/sync', 'Admin\TransactionCrudController@sync');
});

Auth::routes();

Route::get('/home', 'HomeController@index');
