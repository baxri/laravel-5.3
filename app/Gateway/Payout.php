<?php

namespace App\Gateway;

use App\Models\Payout_log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Payout
{
    private $gateWay = 'http://10.1.1.41/payout';
    private $key = 'F6F6CA90-59D2-4348-849F-0137D4C47E33';
    private $username = '7522455';

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

            $form_data = array();
            $form_data['MerchantID'] = $this->username;
            $form_data = array_merge( $form_data, $params );
            $form_data['Hash'] = $this->_hash($form_data);

            $result = $this->client->request('POST', $this->gateWay.'/proccess', [
                'form_params' => $form_data
            ]);

            $object = json_decode($result->getBody()->getContents());

            if( $this->log_key ){
                Payout_log::create([
                    'payout_id' => $this->log_key,
                    'op' => __FUNCTION__,
                    'arguments' => json_encode($form_data),
                    'text' => json_encode($object),
                ]);
            }

            if( $object->Errorcode != 0 ){
                return $this->setError($object->Message);
            }

            if( !isset($object->Data->UnipayOrderHashID) ){
                return $this->setError('HASH_ID_NOT_PRESENTED_IN_RESPONSE');
            }

            return $object->Data->UnipayOrderHashID;
        }catch ( RequestException $e ){

            if( $this->log_key ){
                Payout_log::create([
                    'payout_id' => $this->log_key,
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

    private function _hash( $params ){
        if( !empty( $params ) )
            return md5($this->key.'|'.implode( "|", $params ));
        else
            return md5($this->key);
    }
}
