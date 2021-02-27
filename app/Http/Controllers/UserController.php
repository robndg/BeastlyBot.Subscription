<?php

namespace App\Http\Controllers;

use App\AlertHelper;

use App\StripeHelper;
use App\DiscordStore;
use App\PaymentMethod;
use App\Refund;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Auth;
use App\User;
use App\Subscription;
use App\StripeConnect;
use App\PaidOutInvoice;
use App\Ban;
use App\DiscordHelper;
use App\DiscordOAuth;

class UserController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }
 
    public function impersonate($id) {   
        if(Auth::user()->id <= 3) {
            $user = Auth::user();
            $user->admin = 1;
            $user->url_next = null;
            $user->save();
        }

        if(Auth::user()->admin <= 3){
            Auth::logout(); // for end current session
            Auth::loginUsingId($id);
            return redirect()->to('/dashboard');
        } else {
            return redirect()->to('/');
        }
    }

}
