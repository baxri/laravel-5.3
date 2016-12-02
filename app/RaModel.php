<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RaModel extends Model
{
    protected function asDateTime($value)
    {
        if($value instanceof Carbon) {
            $value->timezone(config('app.timezone'));
        }

        return parent::asDateTime($value);
    }
}
