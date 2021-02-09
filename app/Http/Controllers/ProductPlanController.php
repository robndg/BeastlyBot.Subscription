<?php

namespace App\Http\Controllers;

//use App\plan;
use Illuminate\Http\Request;
use App\DiscordStore;
use App\ProductRole;
use App\Ban;
use App\StripeConnect;
use App\StripeHelper;
use App\Price;
use App\Product;
Use App\StoreCustomer;
use Illuminate\Support\Facades\Cache;

use App\Products\DiscordRoleProduct;
use App\Products\Plans\DiscordPlan;
use App\Products\ProductMsgException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProductPlanController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) { // on subscribe create subscriptions table with this info copied, and add stripe_price_id
        // TODO ROB2: add checks and try catches
        Log::info("here");
        // Setup 
        $price_interval_day = $request->price_interval_day;
        $price_interval_week = $request->price_interval_week;
        $price_interval_month = $request->price_interval_month;
        $price_interval_year = $request->price_interval_year;

        $price_interval_array = ['day' => $price_interval_day, 'week' => $price_interval_week, 'month' => $price_interval_month, 'year' => $price_interval_year];
        
        
        //$price_id = $request->price_id;
        $product_id = $request->product_id;
        $stripe_price_id = null; // add on subscribe
        $paypal_price_id = null; // add on subscribe
        $assigned_to = null; // add on subscribe
        $start_date = null;
        $end_date = null;
        $max_sales = null;
        $discount = null;
        $discount_end = null;
        $discount_max = null;
        $status = 1;

        $cur = "usd";

        Log::info("ProductId");
        Log::info($product_id);

        if($product_id == 0){
            return response()->json(['success' => false, 'msg' => 'Please refresh the page and try again.']);
        }
       
 
        // foreach interval
        foreach (["day", "week", "month", "year"] as $price_interval) {
           
            $interval = $price_interval; // ex 'day'
            Log::info($interval);
            $price = $price_interval_array[$price_interval]; // null or 0.00
            Log::info($price);
            if($price == null || $price == 0 || $price == "NULL" || $price == NULL){
                $price == null;
                $status = 0; // might make free available later
                $update_price = Price::where('product_id', $product_id)->where('interval', $interval)->update(['price' => null]);
            }else{
                $price = intval($price * 100);
                ProductRole::where('id', $product_id)->update(['active' => 1]);
            }
            // disable old product prices
            $disabled = Price::where('product_id', $product_id)->where('interval', $interval)/*->with('assigned', '!=', null)*/->update(['status' => 0]); // 2 is updated to new one
            Log::info($disabled);
            // create new product price
           /* $create_new_bool = true;
            if(Price::where('product_id', $product_id)->where('interval', $interval)->with('assigned', null)->with('active', 1)->first()){
                $getpricehere = Price::where('product_id', $product_id)->where('interval', $interval)->with('assigned', null)->with('active', 1)->first();
                if($price != $getpricehere->price){
                    $create_new_bool = false;
                }
                
            }*/
           // Log::info([$price_id, $product_id, $stripe_price_id, $paypal_price_id, $price, $cur, $interval, $assigned_to, $start_date, $end_date, $max_sales, $discount, $discount_end, $discount_max, $status, null]);
            if($price != null && $price > 0 && $price != "NULL" && $price != NULL){
                $product_price = new Price(['id' => Str::uuid(), 'product_id' => $product_id, 'stripe_price_id' => $stripe_price_id, 'paypal_price_id' => $paypal_price_id, 'price' => $price, 'cur' => $cur, 'interval' => $interval, 'assigned_to' => $assigned_to, 'start_date' => $start_date, 'end_date' => $end_date, 'max_sales' => $max_sales, 'discount' => $discount, 'discount_end' => $discount_end, 'discount_max' => $discount_max, 'status' => $status, 'metadata' => null]);
                $product_price->save();
            }
            // Create plan on subscribe
          
        }
        
        return response()->json(['success' => true, 'msg' => 'Plan Saved!', 'active' => true]);
    }


        public static function getPricesForProduct($product_id) { // Used in slide roles settings
         /*   $prices = [];
            
            foreach (["day", "week", "month", "year"] as $interval) {
                $product_prices = Price::where('product_id', $product_id)->where('interval', $interval)->where('status', '<', 2)->get();
                if($product_prices->status == 1){
                    $prices[$interval] = $product_prices / 100;
                }else{
                    $prices[$interval] = null;
                }
                // TODO ROB2: move this to checkout;
                // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
           
            }
            return $prices;*/
        }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function show(plan $plan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function edit(plan $plan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, plan $plan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy(plan $plan)
    {
        //
    }
}
