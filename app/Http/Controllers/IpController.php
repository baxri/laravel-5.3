<?php

namespace App\Http\Controllers;

use App\Gateway\Ip;

class IpController extends Controller
{
    public function current(){
        return response()->ok(Ip::current(true));
    }
}
