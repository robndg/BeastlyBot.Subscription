<?php

namespace App\Http\Controllers;

use App\SiteConfig;
use App\Product;
use App\Products\DiscordRoleProduct;
use App\Products\ProductMsgException;
use App\RoleDesc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\InvalidRequestException;

class ProductController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public function createProduct(Request $request) {
        try {
            switch ($request['product_type']) {
                case "discord":
                    $product = new DiscordRoleProduct($request['guild_id'], $request['role_id'], $request['billing_cycle']);
                break;
                default:
                    throw new ProductMsgException('Could not find product by that type.');
                break;
            }
            \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));
            return $product->create($request);
        } catch(ProductMsgException $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        } catch(\Stripe\Exception\ApiErrorException $e) {
            if(env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false, 'msg' => $e->getError()->message]);
        }
    }

    public function deleteProduct(Request $request) {
        try {
            switch ($request['product_type']) {
                case "discord":
                    $product = new DiscordRoleProduct($request['guild_id'], $request['role_id'], $request['billing_cycle']);
                break;
                default:
                    throw new ProductMsgException('Could not find product by that type.');
                break;
            }
            \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));
            return $product->delete($request);
        } catch(ProductMsgException $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        } catch(\Stripe\Exception\ApiErrorException $e) {
            if(env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false, 'msg' => $e->getError()->message]);
        }
    }

    public function updateProduct(Request $request) {
        try {
            switch ($request['product_type']) {
                case "discord":
                    $product = new DiscordRoleProduct($request['guild_id'], $request['role_id'], $request['billing_cycle']);
                break;
                default:
                    throw new ProductMsgException('Could not find product by that type.');
                break;
            }
            \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));
            return $product->update($request);
        } catch(ProductMsgException $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        } catch(\Stripe\Exception\ApiErrorException $e) {
            if(env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false, 'msg' => $e->getError()->message]);
        }
    }

    // TODO: Make sure guild_id and role_id are valid
    public function updatePrices(Request $request) {
        $guild_id = $request['guild_id'];

        if(!\auth()->user()->ownsGuild($guild_id)) {
            return response()->json(['success' => false, 'msg' => 'You are not the owner of this server.']);
        }

        if(\auth()->user()->error == '1') {
            return response()->json(['success' => false, 'msg' => 'You must connect a new Stripe account']);
        }
        $role_name = $request['role_name'];

        $role_id = $request['role_id'];
        $prices = [
            1 => str_replace(',', '', $request['price_1_month']),
            3 => str_replace(',', '', $request['price_3_month']),
            6 => str_replace(',', '', $request['price_6_month']),
            12 => str_replace(',', '', $request['price_12_month'])];
        $durations = array(1, 3, 6, 12);

        // Check if the user is a partner. If not they have no room being here.
        if (auth()->user()->stripe_express_id == null) return response()->json(['success' => false, 'msg' => 'Please connect or create your stripe account.']);

        // Check if the price inputted for each duration is valid
        foreach ($durations as $duration) {
            if (!$this->validateCurrency($prices[$duration])) return response()->json(['success' => false, 'msg' => 'Invalid price for ' . $duration . ' month.']);
        }

        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(SiteConfig::get('STRIPE_SECRET'));
        if(($prices[1] || $prices[3] || $prices[6] || $prices[12]) != 0){
            \Stripe\Product::update(
                $guild_id . '_' . $role_id,
                ['active' => true]
            );
        }else{
            \Stripe\Product::update(
                $guild_id . '_' . $role_id,
                ['active' => 'false']
            );
            Product::deleteEntireProduct($guild_id, $role_id);
        }
        // Grab the product from Stripe and make sure it is valid
        try {
            $product = \Stripe\Product::retrieve($guild_id . '_' . $role_id);
            if (!$product->active) throw new InvalidRequestException();
        } catch (InvalidRequestException $e) {
            if (env('APP_DEBUG')) Log::error($e);
            //$enabled_a = 'false';
            if(($prices[1] || $prices[3] || $prices[6] || $prices[12]) != 0){
                return response()->json(['success' => false, 'msg' => 'You must enable this role first.']);
            }else{
                // Now we will iterate through all the inputted durations
                foreach ($durations as $duration) {
                    // If the price is greater than $0 we must act on it
                        try {
                            // Check if the plan exists by retrieving it. If it doesn't exist the Exception will be thrown
                            $plan = \Stripe\Plan::retrieve($guild_id . '_' . $role_id . '_' . $duration . '_r');
                            $plan->delete();
                        }catch (\Exception $e) {
                            if (env('APP_DEBUG')) Log::error('Could not delete plan ' . $guild_id . '_' . $role_id . '_' . $duration . '_r');
                        }
                }
                return response()->json(['success' => false, 'msg' => 'Product Disabled.']);
            }
        }

        // Make sure the current user is the owner of the Stripe product
        if ($product->metadata->toArray()['stripe_express_id'] != auth()->user()->stripe_express_id) return response()->json(['success' => false, 'msg' => 'You are not the owner of this role and guild.']);
        //if ($product->metadata->toArray()['id'] != auth()->user()->id) return response()->json(['success' => false, 'msg' => 'You are not the owner of this role and guild.']);
        $price_total = 0;
        try {
            // Now we will iterate through all the inputted durations
            foreach ($durations as $duration) {
                // If the price is greater than $0 we must act on it
                if ($prices[$duration] > 1) {
                    try {
                        // Check if the plan exists by retrieving it. If it doesn't exist the Exception will be thrown
                        $plan = \Stripe\Plan::retrieve($guild_id . '_' . $role_id . '_' . $duration . '_r');

                        // Check if the price on Stripe is different than what the user inputted. If it is we need to update
                        // it on Stripe.
                        if (($plan->amount / 100) != $prices[$duration]) {
                            // Instead of updating the plan go ahead and delete it then just create a new one.
                            $plan->delete();
                            \Stripe\Plan::create([
                                "amount" => $prices[$duration] * 100,
                                "interval" => "month",
                                "interval_count" => $duration,
                                "product" => $guild_id . '_' . $role_id,
                                "currency" => "usd",
                                'metadata' => [
                                    'id' => auth()->user()->id,
                                    'stripe_express_id' => auth()->user()->stripe_express_id,
                                    'app_fee_percent' => auth()->user()->app_fee_percent,
                                    'payout_delay' => auth()->user()->stripe_delay_days
                                ],
                                "id" => $guild_id . '_' . $role_id . '_' . $duration . '_r',
                                "nickname" => $role_name . ' - ' . $duration . '/mo',
                            ]);

                            Product::createProduct($guild_id, $role_id, $duration);
                        }
                    } catch (\Exception $e) {
                        // If the Exception is thrown then most likely we couldn't find the plan and we create it
                        \Stripe\Plan::create([
                            "amount" => $prices[$duration] * 100,
                            "interval" => "month",
                            "interval_count" => $duration,
                            "product" => $guild_id . '_' . $role_id,
                            "currency" => "usd",
                            'metadata' => [
                                'id' => auth()->user()->id,
                                'stripe_express_id' => auth()->user()->stripe_express_id,
                                'app_fee_percent' => auth()->user()->app_fee_percent,
                                'payout_delay' => auth()->user()->stripe_delay_days

                            ],
                            "id" => $guild_id . '_' . $role_id . '_' . $duration . '_r',
                            "nickname" => $role_name . ' - ' . $duration . '/mo',
                        ]);

                        Product::createProduct($guild_id, $role_id, $duration);
                    }
                    $price_total += $prices[$duration];
                }else{
                    $price_total += 0;
                }
            }

        } catch (InvalidRequestException $e) {
            if (env('APP_DEBUG')) Log::error($e);
        }

        return response()->json(['success' => true, 'msg' => 'Prices updated.']);
    }

    public function checkProductPrices(Request $request){
        $guild_id = $request['guild_id'];
        $role_id = $request['role_id'];
        $prices_total = 0;

        $prices = [];
        $active_roles = [];
        $durations = array(1, 3, 6, 12);
            try {

                // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

                foreach ($durations as $duration) {
                    try {
                        $plan = \Stripe\Plan::retrieve($guild_id . '_' . $role_id . '_' . $duration . '_r');
                        $prices_total += $plan->amount;
                        //$active_roles += array($role_id);
                        //return true;
                        //return $active_roles;
                        //return true;
                    } catch (\Exception $e){
                        $prices_total += 0;
                        //return true;
                    }
                }
                if($prices_total == 0){
                    \Stripe\Product::update(
                        $guild_id . '_' . $role_id,
                        ['active' => false]
                    );
                    //return response()->json(['success' => false, 'msg' => 'inactive-role']);
                    return response()->json(['success' => false]);
                }else{
                   // return true;
                   // return response()->json(['success' => true, 'msg' => 'active-role']);
                    return response()->json(['success' => true]);
                }
                //return true;
            } catch (\Exception $e) {
                if (env('APP_DEBUG')) Log::error($e);
                return response()->json(['success' => false, 'msg' => $e->getMessage()]);
            }

    }


    public function toggleProductActivity(Request $request) {
        $guild_id = $request['guild_id'];

        if(!\auth()->user()->ownsGuild($guild_id)) {
            return response()->json(['success' => false, 'msg' => 'You are not the owner of this server.']);
        }

        if(\auth()->user()->error == '1') {
            return response()->json(['success' => false, 'msg' => 'You must connect a new Stripe account']);
        }

        $role_id = $request['role_id'];
        $guild_name = $request['guild_name'];
        $role_name = $request['role_name'];

        // Check if the user is a partner. If not then they have no business being here.
        if (auth()->user()->stripe_express_id == null) return response()->json(['success' => false, 'msg' => 'Please connect or create your Stripe account.']);

        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $active = false;

        try {
            /**
             * So first we try to grab the product from Stripe. If we can't find it the Exception is thrown at the bottom.
             * If that is called we create a new product since it doesn't exist yet.
             **/
            $product = \Stripe\Product::retrieve($guild_id . '_' . $role_id);

            // If the product exists we will make it here and we update it.
            try {
                \Stripe\Product::update(
                    $guild_id . '_' . $role_id,
                    ['active' => !$product->active,
                    'metadata' => ['id' => auth()->user()->id,
                    'stripe_express_id' => auth()->user()->stripe_express_id,
                    'app_fee_percent' => auth()->user()->app_fee_percent,
                    'payout_delay' => auth()->user()->stripe_delay_days]
                    ]
                );

                $active = !$product->active;

                if($active == false){
                    Product::deleteEntireProduct($guild_id, $role_id);
                   // return response()->json(['success' => true, 'msg' => 'Product disabled.', 'active' => $active]);
                }
            } catch (\Exception $e) {
                if (env('APP_DEBUG')) Log::error($e);
                return response()->json(['success' => false, 'msg' => $e->getMessage()]);
            }
        } catch (\Exception $e) {
            // If the product could not be found we create one instead of disabling an existing one.
            try {
                $product = \Stripe\Product::create([
                    'name' => $guild_name . "'s " . $role_name . " role.",
                    'id' => $guild_id . '_' . $role_id,
                    'type' => 'service',
                    'metadata' => [
                        'id' => auth()->user()->id,
                        'stripe_express_id' => auth()->user()->stripe_express_id,
                        'app_fee_percent' => auth()->user()->app_fee_percent,
                        'payout_delay' => auth()->user()->stripe_delay_days
                    ]
                ]);
                $active = true;
                return response()->json(['success' => true, 'msg' => 'Product created.', 'active' => $active]);
            } catch (\Exception $e) {
                if(env('APP_DEBUG')) Log::error($e);
                return response()->json(['success' => false, 'msg' => $e->getMessage()]);
            }
        }

        return response()->json(['success' => true, 'msg' => 'Product created.', 'active' => $active]);
    }

    function validateCurrency($input) {
        return $input === null || $input === '' || preg_match('/^[0-9]+(?:\.[0-9]{0,2})?$/', $input);
    }

    public static function getPricesForRole($guild_id, $role_id) {
        $prices = [];
        $durations = array(1, 3, 6, 12);

        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        foreach ($durations as $duration) {
            try {
                $plan = \Stripe\Plan::retrieve($guild_id . '_' . $role_id . '_' . $duration . '_r');
                $prices[$duration] = $plan->amount / 100;
            } catch (\Exception $e) {
                $prices[$duration] = 0;
            }
        }

        return $prices;
    }


    public static function getPricesForSpecial($guild_id, $role_id, $discord_id) {
        $prices = [];
        $durations = array(1, 3, 6, 12);
        // Any time accessing Stripe API this snippet of code must be ran above any preceding API calls
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        foreach ($durations as $duration) {
            try {
                $plan = \Stripe\Plan::retrieve($guild_id . '_' . $role_id . '_' . $duration . '_r_s_' . $discord_id);
                $prices[$duration] = $plan->amount / 100;
            } catch (\Exception $e) {
                //$prices[$duration] = -1;
                try {
                    $plan = \Stripe\Plan::retrieve($guild_id . '_' . $role_id . '_' . $duration . '_r_s_' . $discord_id);
                    $prices[$duration] = $plan->amount / 100;
                } catch (\Exception $e) {
                    $prices[$duration] = -1;
                }
            }
        }

        return $prices;
    }

    public function setProductDescription(Request $request) {
        $guild_id = $request['guild_id'];

        if(!\auth()->user()->ownsGuild($guild_id)) {
            return response()->json(['success' => false, 'msg' => 'You are not the owner of this server.']);
        }

        if(auth()->user()->error == '1') {
            return response()->json(['success' => false, 'msg' => 'You must connect a new Stripe account']);
        }

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
