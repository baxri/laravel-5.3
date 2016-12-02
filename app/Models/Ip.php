<?php

namespace App\Models;

use App\RaModel;
use Backpack\CRUD\CrudTrait;

class Ip extends RaModel
{
    protected $fillable = [
        'ip_key', 'as', 'city', 'country', 'countryCode', 'isp', 'lat', 'lon', 'org', 'query', 'region', 'regionName', 'status',
        'timezone', 'zip'
    ];

	use CrudTrait;

	protected $table = 'ips';
	protected $primaryKey = 'id';

}
