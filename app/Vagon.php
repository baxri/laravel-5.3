<?php

namespace App;

use App\helpers\Railway;

class Vagon extends RaModel
{
    protected $fillable = [
        'class', 'rank', 'name', 'amount', 'enable', 'train'
    ];

    public function toArray()
    {
        return [
            'train' => $this->train,
            'class_name' => $this->class,
            'rank' => $this->rank,
            'name' => Railway::translate($this->name),
            'amount' => number_format( $this->amount/100, 2 ),
            'enable' => $this->enable,
        ];
    }
}
