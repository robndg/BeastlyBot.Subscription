<?php

namespace App;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use League\OAuth2\Client\Token\AccessToken;
use Wohali\OAuth2\Client\Provider\Discord;

class AlertHelper
{

    public static function alertSuccess($message) {
        Session::put('alert', ['type' => 'success', 'msg' => $message]);
    }

    public static function alertError($message) {
        Session::put('alert', ['type' => 'error', 'msg' => $message]);
    }

    public static function alertWarning($message) {
        Session::put('alert', ['type' => 'warning', 'msg' => $message]);
    }

    public static function alertInfo($message) {
        Session::put('alert', ['type' => 'info', 'msg' => $message]);
    }

    public static function alertQuestion($message) {
        Session::put('alert', ['type' => 'question', 'msg' => $message]);
    }

}
