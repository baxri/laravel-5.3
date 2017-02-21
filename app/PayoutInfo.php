<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayoutInfo extends Model
{
    protected $table = 'payout_info';

    protected $fillable = ['email', 'index', 'mobile', 'index_mobile', 'name', 'surname', 'idnumber', 'birth_date', 'iban'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($PayoutInfo) {
            $PayoutInfo->index_mobile = $PayoutInfo->index.$PayoutInfo->mobile;
        });

        static::updating(function ($PayoutInfo) {
            $PayoutInfo->index_mobile = $PayoutInfo->index.$PayoutInfo->mobile;
        });
    }

}

