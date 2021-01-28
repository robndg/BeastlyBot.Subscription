<?php

namespace App\Http\Controllers;

use App\DiscordStore;
use App\ProductRole;
use App\Ban;
use App\StripeConnect;
use App\StripeHelper;
use App\Price;
use App\Product;

use Illuminate\Support\Facades\Cache;

use App\Products\DiscordRoleProduct;
use App\Products\Plans\DiscordPlan;
use App\Products\ProductMsgException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DeletedPlanController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }


    public function create(Request $request) { // on subscribe create subscriptions table with this info copied, and add stripe_price_id
        // TODO ROB2: add checks and try catches

        // Setup 
        $price_interval_day = $request->price_interval_day;
        $price_interval_week = $request->price_interval_week;
        $price_interval_month = $request->price_interval_month;
        $price_interval_year = $request->price_interval_year;

        $price_interval_array = ['day' => $price_interval_day, 'week' => $price_interval_week, 'month' => $price_interval_month, 'year' => $price_interval_year];
        

        $price_id = $request->price_id;
        $product_id = $request->product_id;
        $stripe_price_id = null; // add on subscribe
        $paypal_price_id = null; // add on subscribe
        $start_date = null;
        $end_date = null;
        $max_sales = null;
        $discount = null;
        $discount_end = null;
        $discount_max = null;
        $status = 1;

        // foreach interval
        foreach ($price_interval_array as $price_interval) {
            $interval = $price_interval[0]; // ex 'day'
            $price = $price_interval[1]; // null or 0.00
            if($price == null || $price == 0){
                $price == null;
                $status = 0; // might make free available later
            }else{
                $price = intval($price * 100);
            }
            // disable old product prices
            $disabled = Price::where('product_id', $product_id)->where('interval', $interval)->update(['status' => 2]); // 2 is updated to new one
            Log::info($disabled);
            // create new product price
            $product_price = new Price(Str::uuid(), $product_id, $stripe_price_id, $paypal_price_id, $price, $cur, $interval, $assigned_to, $start_date, $end_date, $max_sales, $discount, $discount_end, $discount_max, $status);
            $product_price->save();
            // Create plan on subscribe
          
        }
        
        
        return response()->json(['success' => true, 'msg' => 'Plan Saved!', 'active' => true]);
    }


        public static function getPricesForProduct($product_id) { // Used in slide roles settings
            $prices = [];
            
            foreach (["day", "week", "month", "year"] as $interval) {
                $product_prices = Price::where('product_id', $product_id)->where('interval', $interval)->where('status', '<', 2)->get();
                if($product_prices->status == 1){
                    $prices[$duration] = $product_prices / 100;
                }else{
                    $prices[$duration] = null;
                }
                // TODO ROB2: move this to checkout;
                // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
                //StripeHelper::setApiKey();
                /*if($discord_plan->getStripePlan() != null) {
                    $prices[$duration] = $discord_plan->getStripePlan()->unit_amount / 100;
                } else {
                    $prices[$duration] = 0;
                }*/
            }
            return $prices;
        }




}