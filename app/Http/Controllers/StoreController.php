<?php

namespace App\Http\Controllers;

use Auth;
use App\AlertHelper;
use App\DiscordStore;
#use App\Shop;
use App\User;
use App\Product;
use App\ProductRole;
use App\Price;
use App\Products\DiscordRoleProduct;
use App\Refund;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Stripe\Exception\InvalidRequestException;
use App\DiscordHelper;
use App\Subscription;
use App\PaidOutInvoice;
use App\Stat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\StripeHelper;
use Illuminate\Support\Str;
use App\ProductPlanController;
use App\Exception;

class StoreController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }


    public function getStoreProduct(){
        if(Auth::check()){
            $stripe_helper = auth()->user()->getStripeHelper();
            $discord_helper = new DiscordHelper(auth()->user());

            return view('store.product-page')->with('stripe_helper', $stripe_helper)->with('discord_helper', $discord_helper);

        }else{
            return view('discord_login');
        }
    }


}