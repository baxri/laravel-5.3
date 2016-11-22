<?php

namespace App\Gateway;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\QueryException;

class Ip
{
    public static function get( $ip ){
        try{

            $ips = \App\Models\Ip::where('ip_key', $ip)->get();

            if(count($ips) > 0){
                return true;
            }

            $client = new Client([
                'base_url' => 'http://ip-api.com/json/',
                'timeout'  => config('railway.guzzle_timeout'),
            ]);

            $stations = $client->request('GET', 'http://ip-api.com/json/'.$ip, [

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

                    \App\Models\Ip::create($info);

                }catch( QueryException $e ){

                }

                return $object;
            }

            return [];
        }catch ( RequestException $e ){
            return [];
        }
    }

    public static function current(){

        $ip = $_SERVER['REMOTE_ADDR'];

        if( strpos( $ip, '192' ) == 0 ){
            $ip = '31.146.160.104';
        }

        self::get($ip);

        return $ip;
    }

}