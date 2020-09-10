<?php

namespace App\Http\Controllers;

use App\AlertHelper;
use App\SiteConfig;
use App\Order;
use App\Products\DiscordRoleProduct;
use App\Products\ExpressProduct;
use App\Products\ProductMsgException;
use App\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Stripe\Exception\InvalidRequestException;
use App\User;

class OrderController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public function setup(Request $request) {
        if (! auth()->user()->hasStripeAccount()) 
            return response()->json(['success' => false, 'msg' => 'You do not have a linked stripe account.']);

        try {
            switch ($request['product_type']) {
                case "discord":
                    $product = new DiscordRoleProduct($request['guild_id'], $request['role_id'], $request['billing_cycle']);
                break;
                case "express":
                    $product = new ExpressProduct($request['billing_cycle'] == '1' ? SiteConfig::get('MONTHLY_PLAN') : SiteConfig::get('YEARLY_PLAN'));
                break;
                default:
                    throw new ProductMsgException('Could not find product by that type.');
                break;
            }

            $product->checkoutValidate();
        } catch(ProductMsgException $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        } catch(\Stripe\Exception\ApiErrorException $e) {
            if(env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false, 'msg' => $e->getError()->message]);
        }

        $stripe_customer = auth()->user()->getStripeHelper()->getCustomerAccount();
        if($product->getApplicationFee() > 0) {
            $checkout_data = [
                'payment_method_types' => ['card'],
                'subscription_data' => [
                    'application_fee_percent' => $product->getApplicationFee(),
                    'items' => [[
                        'plan' => $product->getStripePlan()->id,
                        'quantity' => '1'
                    ]]
                ],
                'success_url' => $product->getCallbackSuccessURL(),
                'cancel_url' => $product->getCallbackCancelURL(),
                'customer' => $stripe_customer->id,
            ];
        } else {
            $checkout_data = [
                'payment_method_types' => ['card'],
                'subscription_data' => [
                    'items' => [[
                        'plan' => $product->getStripePlan()->id,
                        'quantity' => '1'
                    ]]
                ],
                'success_url' => $product->getCallbackSuccessURL(),
                'cancel_url' => $product->getCallbackCancelURL(),
                'customer' => $stripe_customer->id,
            ];
        }

        // This may have to go in the second argument of Session::create
        if($product->getExpressOwnerID() != null) $checkout_data['payment_intent_data'] = ['on_behalf_of' => $product->getExpressOwnerID()];
        
        $session = \Stripe\Checkout\Session::create($checkout_data);

        // store the checkout ID in the customers CheckoutSession
        Session::put('checkout_id', $session->id);
        return response()->json(['success' => true, 'msg' => $session->id]);
    }

    public function checkout() {
        $success = \request('success');

        if($success)
            AlertHelper::alertSuccess('Payment successful!');
        else 
            AlertHelper::alertInfo('Payment cancelled.');

        // If the customer does not exist we have to cancel the order as we won't have a stripe account to charge
        if (! auth()->user()->hasStripeAccount())  {
            AlertHelper::alertError('You do not have a linked stripe account.');
            return redirect('/dashboard');
        }

        try {
            switch (\request('product_type')) {
                case "discord":
                    $product = new DiscordRoleProduct(\request('guild_id'), \request('role_id'), \request('billing_cycle'));
                break;
                case "express":
                    $product = new ExpressProduct(\request('plan_id'));
                break;
                default:
                    throw new ProductMsgException('Could not find product by that type.');
                break;
            }
        } catch(ProductMsgException $e) {
            AlertHelper::alertWarning($e->getMessage());
        } catch(\Stripe\Exception\ApiErrorException $e) {
            if(env('APP_DEBUG')) Log::error($e);
            AlertHelper::alertWarning($e->getError()->message);
        }

        if($success) {
            $session = \Stripe\Checkout\Session::retrieve(Session::get('checkout_id'));
            $order = new Order();
            $order->id = $session->subscription;
            $order->save();
        }

        Session::remove('checkout_id');
        return $success ? $product->checkoutSuccess() : $product->checkoutCancel();
    }

    public function changePlan(Request $request) {
        try {
            switch ($request['product_type']) {
                case "express":
                    $product = new ExpressProduct(null, $request['plan_id']);
                break;
                default:
                    throw new ProductMsgException('Could not find product by that type.');
                break;
            }

            return $product->changePlan($request['plan_id']);
        } catch(ProductMsgException $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        } catch(\Stripe\Exception\ApiErrorException $e) {
            if(env('APP_DEBUG')) Log::error($e);
            return response()->json(['success' => false, 'msg' => $e->getError()->message]);
        }
    }

}
