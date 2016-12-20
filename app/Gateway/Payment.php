<?php

namespace App\Gateway;

use App\Models\TransactionLog;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Mockery\Exception;

class Payment
{
    private $gateWay = 'http://10.1.1.41/checkout';
    private $key = 'C5E49E25-D5CF-4331-8AB1-60571EDDF08B';
    private $username = '7508719';

    private $client;
    private $_error;

    public $redirect = null;
    public $payment_hash = null;

    public static $success = 'COMPLETED';

    public static $error_return_money = '400';
    public static $error_dublicated_request = '208';

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

    public function create( array $params ){

        try{

            $form_data = array();

            $form_data['MerchantID'] = $this->username;
            $form_data['MerchantOrderID'] = $params['transaction_id'];
            $form_data['OrderPrice'] = $params['amount'];
            $form_data['BackLink'] = base64_encode($params['success'].'|'.$params['cancel']);
            //$form_data['OrderName'] = $params['order_name'];
           // $form_data['OrderDescription'] = $params['description'];
            $form_data['Language'] = $params['language'];

            $form_data['Hash'] = $this->_hash($form_data);

            if( isset($params['items'][1]) ){
                $form_data['Items'] = [ $params['items'][0], $params['items'][1] ];
            }else{
                $form_data['Items'] = [$params['items'][0]];
            }

            $stations = $this->client->request('POST', $this->gateWay.'/createorder', [
                'form_params' => $form_data
            ]);

            $object = json_decode($stations->getBody()->getContents());

            if( $this->log_key ){
                TransactionLog::create([
                    'transaction_id' => $this->log_key,
                    'op' => 'checkout',
                    'arguments' => json_encode($form_data),
                    'text' => json_encode($object),
                ]);
            }

            if( $object->Errorcode != 0 ){
                return $this->setError($object->Message);
            }

            if( empty( $object->Data->Checkout ) ){
                return $this->setError('CHECKOUT_URL_IS_EMPTY');
            }

            if( empty( $object->Data->UnipayOrderHashID ) ){
                return $this->setError('CHECKOUT_DOCUMENT_NUMBER_EMPTY');
            }

            $this->redirect = $object->Data->Checkout;
            $this->payment_hash = $object->Data->UnipayOrderHashID;

            return true;
        }catch ( RequestException $e ){

            if( $this->log_key ){
                TransactionLog::create([
                    'transaction_id' => $this->log_key,
                    'op' => 'checkout',
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

    public static function autoReversal( $reason ){
        throw new Exception($reason, Payment::$error_return_money);
    }

    private function _hash( $params ){
        if( !empty( $params ) )
            return md5($this->key.'|'.implode( "|", $params ));
        else
            return md5($this->key);
    }
}
