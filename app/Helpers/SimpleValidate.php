<?php

namespace App\Helpers;
use Illuminate\Support\Facades\DB;

class SimpleValidate {
    public static function checkApikeyAndDevice($apikey = null, $device = null)
    {
        return $id = (!is_null($apikey) & !is_null($device)) ?
            json_decode(json_encode(DB::table('devices')
            ->select('id')
            ->where('users_apikey', '=', $apikey)
            ->where('name', '=', $device)
            ->first()), true) :
            json_decode(json_encode(DB::table('users')
            ->select('id')
            ->where('apikey', '=', $apikey)
            ->first()), true);
    }
}