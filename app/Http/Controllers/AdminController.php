<?php

namespace App\Http\Controllers;

use App\SiteConfig;
use Exception;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }

    public function setSiteConfigValue(Request $request) {
        ## Send the unlock key, setting name and new setting string

        $req_unlock_code = $request['unlock_code'];
        $req_update_name = $request['key'];
        $req_update_string = $request['value'];

        ## Check if unlock key correct, then update DB
        try {
        if ($req_unlock_code === "99CR%")
            SiteConfig::set($req_update_name, $req_update_string);
        else throw new Exception('Invalid passcode.');
        } catch (Exception $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
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
