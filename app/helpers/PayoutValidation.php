<?php

namespace App\helpers;

abstract class PayoutValidation
{
    public static function checkname( $name ){

        if( strlen( $name ) < 3 ){
            return false;
        }

        return true;
    }

    public static function checkIBan( $iban ){

        if( strlen( $iban ) != 22 ){
            return false;
        }

        $bank = substr($iban, 4, 2);

        if( empty($bank) ){
            return false;
        }

        return $bank;
    }

    public static function checkIDnumber( $idnumber ){

        if( strlen( $idnumber ) != 11 ){
            return false;
        }

        return true;
    }

    public static function checkBirthDate( $birth_date ){

        $birth_date = explode( '-', $birth_date );

        if( count( $birth_date ) != 3 ){
            return false;
        }

        return true;
    }
}