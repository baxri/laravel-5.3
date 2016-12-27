<?php

namespace App\Gateway;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\QueryException;

class Ip
{
    public static $gateway = 'http://ip-api.com/json/';

    public static function get( $ip ){
        try{

            $ips = \App\Models\Ip::where('ip_key', $ip)->get();

            if(count($ips) > 0){
                return $ips[0]->toArray();
            }

            $client = new Client([
                'base_url' => self::$gateway,
                'timeout'  => config('railway.guzzle_timeout'),
            ]);

            $stations = $client->request('GET', self::$gateway.$ip, [

            ]);

            $object = json_decode($stations->getBody()->getContents());

            if( $object->status == 'success' ){

                try{

                    $info = [
                        'ip_key' => $ip,
                        'as' => $object->as,
                        'city' => $object->city,
                        'country' => $object->country,
                        'countryCode' => $object->countryCode,
                        'isp' => $object->isp,
                        'lat' => $object->lat,
                        'lon' => $object->lon,
                        'org' => $object->org,
                        'query' => $object->query,
                        'region' => $object->region,
                        'regionName' => $object->regionName,
                        'status' => $object->status,
                        'timezone' => $object->timezone,
                        'zip' => $object->zip,
                    ];

                    $object = \App\Models\Ip::create($info);

                    return $object->toArray();

                }catch( QueryException $e ){
                    return [];
                }
            }

            return [];
        }catch ( RequestException $e ){
            return [];
        }
    }

    public static function current( $object = false ){

        $ip = $_SERVER['REMOTE_ADDR'];

        if( strpos( $ip, '192' ) == 0 ){
            $ip = '31.146.160.104';
        }

        $result = self::get($ip);

        if( $object ){
            return $result;
        }

        return $ip;
    }

}
