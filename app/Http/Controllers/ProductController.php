<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Cache;
use App\SiteConfig;
use App\Products\DiscordRoleProduct;
use App\Products\Plans\DiscordPlan;
use App\Products\ProductMsgException;
use App\RoleDesc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public function product(Request $request) {
        $interval_cycle = $request['interval_cycle'];

        try {
            // find the product type to initiate
            switch ($request['product_type']) {
                case "discord":
                    $product = new DiscordRoleProduct($request['guild_id'], $request['role_id'], $interval_cycle);
                break;
                default:
                    throw new ProductMsgException('Could not find product by that type.');
                break;
            }
            // find the action to execute
            \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));
            switch($request['action']) {
                case "create":
                    return $product->create($request);
                case "delete":
                    return $product->delete($request);
                case "update":
                    return $product->update($request);
            }
        } catch(ProductMsgException $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        } catch(\Stripe\Exception\ApiErrorException $e) {
            if(env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false, 'msg' => $e->getError()->message]);
        }
    }

    public function plan(Request $request) {
        $interval = $request['interval'];
        $interval_cycle = $request['interval_cycle'];

        try {
            // find the product type to initiate
            switch ($request['product_type']) {
                case "discord":
                    $plan = new DiscordPlan(new DiscordRoleProduct($request['guild_id'], $request['role_id'], $interval_cycle), $interval, $interval_cycle);
                break;
                default:
                    throw new ProductMsgException('Could not find product by that type.');
                break;
            }
            // find the action to execute
            \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));
            switch($request['action']) {
                case "create":
                    return $plan->create($request);
                case "delete":
                    return $plan->delete($request);
                case "update":
                    return $plan->update($request);
            }
        } catch(ProductMsgException $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        } catch(\Stripe\Exception\ApiErrorException $e) {
            if(env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false, 'msg' => $e->getError()->message]);
        }
    }

    public static function getPricesForRole($guild_id, $role_id) {
        $prices = [];
        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        foreach ([1, 3, 6, 12] as $duration) {
            $key = 'price_' . $guild_id . '_' . $role_id . '_' . $duration;
            if(! Cache::has($key)) {
                $discord_role_product = new DiscordRoleProduct($guild_id, $role_id, $duration);
                if($discord_role_product->getStripePlan() != null)
                    Cache::put($key, $discord_role_product->getStripePlan()->amount / 100, 60 * 5);
                else 
                    Cache::put($key, 0, 60 * 5);
            }

            $prices[$duration] = Cache::get($key, 0);
        }

        return $prices;
    }

    public function setProductDescription(Request $request) {
        $guild_id = $request['guild_id'];

        if(!\auth()->user()->getDiscordHelper()->ownsGuild($guild_id)) {
            return response()->json(['success' => false, 'msg' => 'You are not the owner of this server.']);
        }
        /* --V1
        if(auth()->user()->error == '1') {
            return response()->json(['success' => false, 'msg' => 'You must connect a new Stripe account']);
        }*/

        $role_id = $request['role_id'];
        $description = $request['description'];

        $role_desc = new RoleDesc();
        // Check if a description exists already in DB. If it does grab it.
        if (RoleDesc::where('guild_id', $guild_id)->where('role_id', $role_id)->exists())
            $role_desc = RoleDesc::where('guild_id', $guild_id)->where('role_id', $role_id)->get()[0];

        // if there is no description entered here then remove from DB, else insert/update into the DB
        if (empty($description)) {
            $role_desc->delete();
        } else {
            $role_desc->guild_id = $guild_id;
            $role_desc->role_id = $role_id;
            $role_desc->description = $description;
            $role_desc->save();
        }

        return response()->json(['success' => true]);
    }

}
