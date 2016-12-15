<?php

namespace App\Gateway;

use App\Models\Log;
use App\TicketLog;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Api
{
    private $gateWay = 'http://10.1.1.41/API/Railway/';

    private $client;
    private $_error;

    private $key = null;

    public function __construct()
    {
        $this->client = new Client([
            'base_url' => $this->gateWay,
            'timeout' => config('railway.guzzle_timeout'),
        ]);
    }

    public function setLogKey( $key ){
        $this->key = $key;
    }
    //
    public function GetTimeTableStations(){

        try{

            $stations = $this->client->request('GET', $this->gateWay, [
                'query' => [
                    'op' => 'GetTimeTableStations'
                ]
            ]);

            $object = json_decode($stations->getBody()->getContents());

            return $object->stations;
        }catch ( RequestException $e ){
            return $this->setError( $e->getMessage() );
        }
    }

    public function GetFreePlacePrices( $date, $from, $to ){

        try{

            $date = str_replace( '-', '/', $date );

            $stations = $this->client->request('GET', $this->gateWay, [
                'query' => [
                    'op' => 'GetFreePlacePrices',

                    'LeavingDate' => $date,
                    'StationFrom' => $from,
                    'StationTo' => $to
                ]
            ]);

            $object = json_decode($stations->getBody()->getContents());

            return $object->trains;
        }catch ( RequestException $e ){
            return $this->setError( $e->getMessage() );
        }
    }

    public function searchTrains( $date, $from, $to, $train, $class, $rank, $adults, $childs ){

        try{

            //$date = str_replace( '-', '/', $date );

            $args = [
                'op' => 'SearchTrains',
                'StationFrom' => $from,
                'StationTo' => $to,
                'LeavingDate' => $date,
                'VagonClassId' => $class,
                'VagonRankId' => $rank,
                'NumAdults' => $adults,
                'NumChildren' => $childs,
                'TrainNumber' => $train,
            ];

            $stations = $this->client->request('GET', $this->gateWay, [
                'query' => $args
            ]);

            $object = json_decode($stations->getBody()->getContents());

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => json_encode($args),
                    'text' => json_encode($object),
                ]);
            }

            return $object->train;
        }catch ( RequestException $e ){

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => '',
                    'text' => $e->getMessage(),
                ]);
            }

            return $this->setError( $e->getMessage() );
        }
    }

    public function secondMark( $purchase ){

        try{

            $args = [
                'op' => 'SecondMark',
                'PurchaseId' => $purchase,
            ];

            $stations = $this->client->request('GET', $this->gateWay, [
                'query' => $args
            ]);

            $object = json_decode($stations->getBody()->getContents());


            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => json_encode($args),
                    'text' => json_encode($object),
                ]);
            }

            return $object;
        }catch ( RequestException $e ){

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => '',
                    'text' => $e->getMessage(),
                ]);
            }

            return $this->setError( $e->getMessage() );
        }
    }

    public function UpdatePassengers( $internet_purchase_id, $purchase, $name, $surname, $idnumber ){

        try{

            $args = [
                'op' => 'UpdatePassengers',
                'InternetPurchaseId' => $internet_purchase_id,
                'PurchaseId' => $purchase,
                'Firstname' => $name,
                'Lastname' => $surname,
                'PassportNumber' => $idnumber,
            ];

            $stations = $this->client->request('GET', $this->gateWay, [
                'query' => $args
            ]);

            $object = json_decode($stations->getBody()->getContents());

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => json_encode($args),
                    'text' => json_encode($object),
                ]);
            }

            return $object;
        }catch ( RequestException $e ){

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => '',
                    'text' => $e->getMessage(),
                ]);
            }

            return $this->setError( $e->getMessage() );
        }
    }

    public function GetPriceStudentDiscountInfo( $purchase ){

        try{

            $args = [
                'op' => 'GetPriceStudentDiscountInfo',
                'PurchaseId' => $purchase,
                'Culture' => 'test',
            ];

            $stations = $this->client->request('GET', $this->gateWay, [
                'query' => $args
            ]);

            $object = json_decode($stations->getBody()->getContents());

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => json_encode($args),
                    'text' => json_encode($object),
                ]);
            }

            return $object;
        }catch ( RequestException $e ){

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => '',
                    'text' => $e->getMessage(),
                ]);
            }

            return $this->setError( $e->getMessage() );
        }
    }

    public function GetFormatEdMoneyByRequestID( $request_id ){

        try{

            $args = [
                'op' => 'GetFormatEdMoneyByRequestID',
                'RequestID' => $request_id,
            ];

            $stations = $this->client->request('GET', $this->gateWay, [
                'query' => $args
            ]);

            $object = json_decode($stations->getBody()->getContents());

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => json_encode($args),
                    'text' => json_encode($object),
                ]);
            }

            return $object;
        }catch ( RequestException $e ){

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => '',
                    'text' => $e->getMessage(),
                ]);
            }

            return $this->setError( $e->getMessage() );
        }
    }

    public function PrepareOnlinePayment( $request_guid, $internet_purchase_id, $train_id ){

        try{

            $args = [
                'op' => 'PrepareOnlinePayment',
                'RequestID' => $request_guid,
                'InternetPurchaseId' => $internet_purchase_id,
                'TrainId' => $train_id,
            ];

            $stations = $this->client->request('GET', $this->gateWay, [
                'query' => $args
            ]);

            $object = json_decode($stations->getBody()->getContents());

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => json_encode($args),
                    'text' => json_encode($object),
                ]);
            }

            return $object;
        }catch ( RequestException $e ){

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => '',
                    'text' => $e->getMessage(),
                ]);
            }

            return $this->setError( $e->getMessage() );
        }
    }

    public function GetMoneyAmount( $railway_id ){

        try{

            $args = [
                'op' => 'GetMoneyAmount',
                'TransactionId' => $railway_id,
            ];

            //

            $stations = $this->client->request('GET', $this->gateWay, [
                'query' => $args
            ]);

            $object = json_decode( $stations->getBody()->getContents() );

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => json_encode($args),
                    'text' => json_encode($object),
                ]);
            }

            return $object;
        }catch ( RequestException $e ){

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => '',
                    'text' => $e->getMessage(),
                ]);
            }

            return $this->setError( $e->getMessage() );
        }
    }

    public function insertTransaction( $railway_id, $ticket_id, $amount ){

        try{

            $args = [
                'op' => 'InsertTransaction',
                'transactionId' => 1,
                //'transactionId' => $railway_id,
                'bankTransactionId' => $ticket_id,
                'MoneyAmount' => $amount,
            ];

            $stations = $this->client->request('GET', $this->gateWay, [
                'query' => $args
            ]);

            $object = json_decode($stations->getBody()->getContents());

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => json_encode($args),
                    'text' => json_encode($object),
                ]);
            }

            return $object;
        }catch ( RequestException $e ){

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => '',
                    'text' => $e->getMessage(),
                ]);
            }

            return $this->setError( $e->getMessage() );
        }
    }

    public function CancelPurchase( $purchase_id ){

        try{

            $args = [
                'op' => 'CancelPurchase',
                'PurchaseID' => $purchase_id,
            ];

            $stations = $this->client->request('GET', $this->gateWay, [
                'query' => $args
            ]);

            $object = json_decode($stations->getBody()->getContents());

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => json_encode($args),
                    'text' => json_encode($object),
                ]);
            }

            return $object;
        }catch ( RequestException $e ){

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => '',
                    'text' => $e->getMessage(),
                ]);
            }

            return $this->setError( $e->getMessage() );
        }
    }

    public function GetTransactionStatus( $request_id, $place_count ){

        try{

            $args = [
                'op' => 'GetTransactionStatus',
                'RequestID' => $request_id,
                'PlacesCount' => $place_count,
            ];

            $stations = $this->client->request('GET', $this->gateWay, [
                'query' => $args
            ]);

            $object = json_decode($stations->getBody()->getContents());

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => json_encode($args),
                    'text' => json_encode($object),
                ]);
            }

            return $object;
        }catch ( RequestException $e ){

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => '',
                    'text' => $e->getMessage(),
                ]);
            }

            return $this->setError( $e->getMessage() );
        }
    }

    public function Internet_GetPlaceBytest( $request_id ){

        try{

            $args = [
                'op' => 'Internet_GetPlaceBytest',
                'RequestID' => $request_id,
            ];

            $stations = $this->client->request('GET', $this->gateWay, [
                'query' => $args
            ]);

            $object = json_decode($stations->getBody()->getContents());

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => json_encode($args),
                    'text' => json_encode($object),
                ]);
            }

            return $object;
        }catch ( RequestException $e ){

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => '',
                    'text' => $e->getMessage(),
                ]);
            }

            return $this->setError( $e->getMessage() );
        }
    }

    public function TrainCharacteristics( $leave, $from, $to ){

        try{

            $args = [
                'op' => 'TrainCharacteristics',
                'LeavingDate' => $leave,
                'FromStation' => $from,
                'ToStation' => $to,
                'IsDay' => 0,
            ];

            $stations = $this->client->request('GET', $this->gateWay, [
                'query' => $args
            ]);

            $object = json_decode($stations->getBody()->getContents());

            d($object);



            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => json_encode($args),
                    'text' => json_encode($object),
                ]);
            }

            return $object;
        }catch ( RequestException $e ){

            if( $this->key ){
                Log::create([
                    'ticket_id' => $this->key,
                    'op' => __FUNCTION__,
                    'arguments' => '',
                    'text' => $e->getMessage(),
                ]);
            }

            return $this->setError( $e->getMessage() );
        }
    }

    public function getError()
    {
        return $this->_error;
    }

    public function setError($error)
    {
        $this->_error = $error;
        return false;
    }
}
