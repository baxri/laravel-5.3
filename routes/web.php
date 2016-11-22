<?php

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'raconsole', 'middleware' => 'admin'], function()
{
    CRUD::resource('station', 'Admin\StationCrudController');

    CRUD::resource('ip', 'Admin\ipCrudController');

    CRUD::resource('transaction', 'Admin\TransactionCrudController');
    CRUD::resource('sms', 'Admin\SmsCrudController');

    CRUD::resource('transaction-log', 'Admin\TransactionLogCrudController');
    CRUD::resource('log', 'Admin\LogCrudController');

    CRUD::resource('payout', 'Admin\PayoutTransactionCrudController');
    CRUD::resource('payout-log', 'Admin\Payout_LogCrudController');

    Route::post('person/return/{person}', 'Admin\TransactionCrudController@ret');
    Route::post('transaction/resend/{transaction}', 'Admin\TransactionCrudController@resend');

    Route::get('transaction/ticket/{ticket}/pdf', 'Admin\TransactionCrudController@pdf');
    Route::get('transaction/ticket/{ticket}/html', 'Admin\TransactionCrudController@html');
    Route::post('transaction/ticket/{ticket}/sync', 'Admin\TransactionCrudController@sync');
});
