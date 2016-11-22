<?php

namespace App\Gateway;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Sms
{
    private $gateWay = 'http://api.unipay.com/sms/v1/send';
    private $key = '92CD1108-A0DB-4CA6-89EA-2AC6CB104F74';
    private $username = '7508719';

    private $client;
    private $_error;

    private $log_key = null;

    public function __construct()
    {
        $this->client = new Client([
            'base_url' => $this->gateWay,
            'timeout'  => config('railway.guzzle_timeout'),
        ]);
    }

    public function setLogKey( $key ){
        $this->log_key = $key;
    }

    public function send( array $params ){
        try{
            $form_data = [];
            $form_data['MerchantID'] = $this->username;

            $params = array_merge($form_data, $params);

            $params['Hash'] = $this->_hash($params);

            $stations = $this->client->request('POST', $this->gateWay, [
                'form_params' => $params
            ]);

            $object = json_decode($stations->getBody()->getContents());

            if( $this->log_key ){
                \App\Models\Sms::create([
                    'transaction_id' => $this->log_key,
                    'op' => 'send',
                    'arguments' => json_encode($form_data),
                    'text' => json_encode($object),
                ]);
            }

            if( $object->Errorcode != 0 ){
                return $this->setError($object->Message);
            }

            return true;
        }catch ( RequestException $e ){

            if( $this->log_key ){
                \App\Models\Sms::create([
                    'transaction_id' => $this->log_key,
                    'op' => 'send',
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

    private function _hash( $params ){
        if( !empty( $params ) )
            return md5($this->key.'|'.implode( "|", $params ));
        else
            return md5($this->key);
    }
}
