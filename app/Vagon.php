<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vagon extends Model
{
    protected $fillable = [
        'class', 'rank', 'name', 'amount', 'enable', 'train'
    ];

    public function toArray()
    {
        return [
            'train' => $this->train,
            'class' => $this->class,
            'rank' => $this->rank,
            'name' => $this->name,
            'amount' => number_format( $this->amount/100, 2 ),
            'enable' => $this->enable,
        ];
    }
}
