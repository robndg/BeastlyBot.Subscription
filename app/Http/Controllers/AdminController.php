<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }

    public function siteSettings() {

        ## Prob get array from sql instead
        $site_settings = ['STRIPE_KEY', 'STRIPE_SECRET', 'STRIPE_CLIENT_ID', 'STRIPE_WEBHOOK_SECRET', 'STRIPE_PAYOUT_DELAY', 'EXPESS_PROD_ID', 'MONTHLY_PLAN', 'YEARLY_PLAN', 'STRIPE_CONNECT_LINK', 'SHOP_URL', 'DISCORD_AUTH_REDIRECT', 'BOT_CONNECTION', 'DISCORD_CLIENT_ID', 'DISCORD_SECRET', 'DISCORD_BOT_LINK'];

        return view('admin.site_settings')->with('site_settings', $site_settings);
    }

    public function updateSiteSettings(Request $request) {

        ## Send the unlock key, setting name and new setting string

        $req_unlock_key = $request['unlock_key'];
        $req_update_name = $request['setting_name'];
        $req_update_string = $request['setting_string'];

        $security_key = "99CR%";

        if($req_unlock_key != "99CR%") {
            return response()->json(['success' => false, 'msg' => 'Incorrect Sucurity Key']);
        }
        
        ## Check if unlock key correct, then update DB
        if ($request['unlock_key'] === "99CR%"){
            $site_settings->$req_update_name = $req_update_string;
            $site_settings->save();
        }

        return response()->json(['success' => true]);

    }

    public function listShopOwners() {

        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        // \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        // $shops = Shop::all();

        // $shops_array = $shops->toArray();

        // usort($shops_array, function($a, $b) {
        //     return $b['created_at'] <=> $a['created_at'];
        // });

        // return view('admin.shop_owners')->with('shops', $shops);


        //$shops_array = $shops->toArray();

     //   $owners = \Stripe\Account::all([
        //    'limit' => 100
       // ]);

        //$owners_array = $owners->data;

        //usort($shops_array, function($a, $b) {
        //    return $b['created_at'] <=> $a['created_at'];
        //});

        // return view('admin.shop_owners')->with('owners', $shops);

    }


}
