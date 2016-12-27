<?php

namespace App\Http\Controllers;

use App\Gateway\Ip;
use Illuminate\Http\Request;

class IpController extends Controller
{
    public function current(){
        return response()->ok(Ip::current());
    }
}
