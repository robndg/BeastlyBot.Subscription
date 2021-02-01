<?php

namespace App\Webhooks\Stripe;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\WebhookClient\Models\WebhookCall;
use \App\StripeConnect;
use \App\DiscordOAuth;
use \App\NewSubscription;

use \App\ScheduledInvoicePayout;
use \App\DiscordStore;
use \App\Subscription;
use \App\Stat;
use RestCord\DiscordClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use \App\StripeHelper;

use \App\User;
use \App\Ban;
use \App\DiscordHelper;
use \App\StoreCustomer;
use \App\ProductRole;
use \App\Price;

class PaymentIntentSucceeded implements ShouldQueue
{
    public function handle(WebhookCall $webhookCall) // Set DB status to 1, then fire role and set to 2
    {
        Log::info("PaymentIntentSucceeded Webhook Call");
        Log::info($webhookCall->payload);
        $add_role_bool = false;
        
        //$sub_id = $webhookCall->payload['data']['object']['subscription'];
        //Log::info($sub_id);
        $paid = $webhookCall->payload['data']['object']['charges']['data'][0]['paid'];
        Log::info($paid);
        $latest_invoice_id = $webhookCall->payload['data']['object']['charges']['data'][0]['invoice'];
        Log::info($latest_invoice_id);

        $subscription = Subscription::where('latest_invoice_id', $latest_invoice_id)->first();

        if($paid == true || $paid == 1){

          if($subscription->status == 0 || $subscription->status == 3){ // new or removed paid late (add role again, reset subscription)
            Log::info("Updating Subscription to Paid");
            $add_role_bool = true;
            $status = 1;
            $subscription->first_invoice_paid_at = $webhookCall->payload['created'];
            $subscription->status = $status;
          }elseif($subscription->status == 1){
            Log::info("Retying Add Role to User");
            $add_role_bool = true;
          }
          //$subscription->latest_invoice_id = null; // null for paid latest invoice
          
          $subscription->save();


          if($add_role_bool){
            Log::info("Adding Role and Store Stats");
            $subscription = Subscription::where('latest_invoice_id', $latest_invoice_id)->first();

            $user_id = $subscription->user_id;
           
            $product_role = ProductRole::where('id', $subscription->product_id)->first(); // store product
            $product_price = Price::where('id', $subscription->price_id); // product price

            $discord_store = DiscordStore::where("id", $product_role->discord_store_id)->first();
            $store_customer = StoreCustomer::where('user_id', $user_id)->where('discord_store_id', $subscription->store_id)->first();
            $store_customer->enabled = 1;
            $store_customer->save();
            //$discord_store = DiscordStore::where("guild_id", $guild_id)->first(); // might leave for store transfers
     
            $guild_id = $discord_store->guild_id;
            $role_id = $product_role->role_id;

            $owner_id = $discord_store->user_id;
            $owner_discord_id = DiscordOAuth::where('user_id', $owner_id)->first()->discord_id;

            $discord_helper = new \App\DiscordHelper(User::find($owner_id));
           
            //$guild = $discord_helper->getGuild($guild_id); repeat, moved below


            $bad_guild = false;
            $bad_role = false;
            /*
            Make sure the guild exists. If not cancel and refund
            */
            try {
                $guild = $discord_helper->getGuild($guild_id);
                if($guild == null) {
                  $bad_guild = true;
                }
            } catch (\Exception $e) {
                Log::info($e);
                $bad_guild = true;
            }
            /*
            Make sure the role exists. If not cancel and refund
            */
            try {
                $role = $discord_helper->getRole($guild_id, $role_id);
                if($role == null) {
                    $bad_role = true;
                }
            } catch (\Exception $e) {
                Log::info($e);
                $bad_role = true;
            }

            if(!$bad_guild && !$bad_role) {

              $stats = Stat::where('type', 1)->where('type_id', $discord_store->id)->first();
              $subscribers_active = $stats->data['subscribers']['active'];
              $subscribers_total = $stats->data['subscribers']['total'];
              $stats_data = $stats->data;
              $stats_data['subscribers'] = ['active' => $subscribers_active + 1, 'total' => $subscribers_total + 1];
              $stats->data = $stats_data;
              $stats->save();

              if($subscribers_active >= 100){
                  $level = 1;
              }elseif($subscribers_active >= 10){
                  $level = 2;
              }else{
                  $level = 3;
              }
              $discord_store->level = $level;
              $discord_store->save();

              try{

                $subscription = Subscription::where('latest_invoice_id', $latest_invoice_id)->first();
              
                $customer_discord_id = DiscordOAuth::where('user_id', $user_id)->first()->discord_id;

                $discord_client = new DiscordClient(['token' => env('DISCORD_BOT_TOKEN')]); // Token is required
                
                $isMember = $discord_helper->isMember($guild->id, $discord_helper->getID());

                Log::info("Checking Member and adding if not");

                if (!$isMember) {
                  $discord_client->guild->addGuildMember([
                    'guild.id' => intval($guild_id),
                    'user.id' => intval($customer_discord_id)
                  ]);
                }

                $discord_client->guild->addGuildMemberRole([
                    'guild.id' => intval($guild_id),
                    'role.id' => intval($role_id),
                    'user.id' => intval($customer_discord_id)
                ]);

                $subscription->status = 2;
                $subscription->save();

                //$guild = $discord_helper->getGuild($guild_id);
                //$role = $discord_helper->getRole($guild_id, $role_id);
                $discord_helper->sendMessage('You have successfully subscribed to ' . $role->name . ' role in the ' . $guild->name . ' server!');
              }catch(\Exception $e) {
                Log::info($e);
                $discord_helper->sendMessage('Uh-oh! I couldn\'t add the role your account. Please contact server owner.');
                    
                $discord_error = new \App\DiscordError();
                    $discord_error->guild_id = $guild_id;
                    $discord_error->role_id = $role_id;
                    $discord_error->user_id = $user_id;
                    $discord_error->message = $e->getMessage();

              }

            }
          }
          
        }
        
        


    }

/*

array (
  'id' => 'evt_1IFopYJJYsXa4u2SIUPL1YfO',
  'object' => 'event',
  'account' => 'acct_1FmxdMJJYsXa4u2S',
  'api_version' => '2020-08-27',
  'created' => 1612133743,
  'data' => 
  array (
    'object' => 
    array (
      'id' => 'cs_test_a1pzh6UO6YDha4OIHGei80KCnHBo7wiLt65VzLlwqa59zNBop5w0gCqv0G',
      'object' => 'checkout.session',
      'allow_promotion_codes' => NULL,
      'amount_subtotal' => 2500,
      'amount_total' => 2500,
      'billing_address_collection' => NULL,
      'cancel_url' => 'http://localhost:8080/shop/608894397328785440',
      'client_reference_id' => '788b6065-f815-420f-9d15-8fa2aad94d86',
      'currency' => 'usd',
      'customer' => 'cus_Ir9o6ATvKpAr65',
      'customer_details' => 
      array (
        'email' => 'webhooktest3@gmail.com',
        'tax_exempt' => 'none',
        'tax_ids' => 
        array (
        ),
      ),
      'customer_email' => NULL,
      'livemode' => false,
      'locale' => NULL,
      'metadata' => 
      array (
      ),
      'mode' => 'subscription',
      'payment_intent' => NULL,
      'payment_method_types' => 
      array (
        0 => 'card',
      ),
      'payment_status' => 'paid',
      'setup_intent' => NULL,
      'shipping' => NULL,
      'shipping_address_collection' => NULL,
      'submit_type' => NULL,
      'subscription' => 'sub_IrXvtXxYmOQKGR',
      'success_url' => 'http://localhost:8080/checkout-subscription-success/834975e5-0b5f-4796-839e-c551f5934035',
      'total_details' => 
      array (
        'amount_discount' => 0,
        'amount_tax' => 0,
      ),
    ),
  ),
  'livemode' => false,
  'pending_webhooks' => 1,
  'request' => 
  array (
    'id' => 'req_RXHDClQbxmbztL',
    'idempotency_key' => NULL,
  ),
  'type' => 'checkout.session.completed',
)  
[2021-01-31 22:56:12] local.INFO: PaymentIntentSucceeded Webhook Call  
[2021-01-31 22:56:12] local.INFO: array (
  'id' => 'evt_1IFopZJJYsXa4u2SKaZWVjCS',
  'object' => 'event',
  'account' => 'acct_1FmxdMJJYsXa4u2S',
  'api_version' => '2020-08-27',
  'created' => 1612133743,
  'data' => 
  array (
    'object' => 
    array (
      'id' => 'pi_1IFopWJJYsXa4u2SrTN4caYb',
      'object' => 'payment_intent',
      'amount' => 2500,
      'amount_capturable' => 0,
      'amount_received' => 2500,
      'application' => 'ca_Il7ro20ve0WNT1KhxA1SGVpoZ4Fro6RU',
      'application_fee_amount' => 100,
      'canceled_at' => NULL,
      'cancellation_reason' => NULL,
      'capture_method' => 'automatic',
      'charges' => 
      array (
        'object' => 'list',
        'data' => 
        array (
          0 => 
          array (
            'id' => 'ch_1IFopWJJYsXa4u2Sxb2Jm0ug',
            'object' => 'charge',
            'amount' => 2500,
            'amount_captured' => 2500,
            'amount_refunded' => 0,
            'application' => 'ca_Il7ro20ve0WNT1KhxA1SGVpoZ4Fro6RU',
            'application_fee' => 'fee_1IFopXJJYsXa4u2SBhOZqVDr',
            'application_fee_amount' => 100,
            'balance_transaction' => 'txn_1IFopXJJYsXa4u2S4Zkj0RSD',
            'billing_details' => 
            array (
              'address' => 
              array (
                'city' => NULL,
                'country' => 'CA',
                'line1' => NULL,
                'line2' => NULL,
                'postal_code' => 'V3H 9B9',
                'state' => NULL,
              ),
              'email' => 'webhooktest3@gmail.com',
              'name' => 'Testing',
              'phone' => NULL,
            ),
            'calculated_statement_descriptor' => 'APP PAYMENT',
            'captured' => true,
            'created' => 1612133742,
            'currency' => 'usd',
            'customer' => 'cus_Ir9o6ATvKpAr65',
            'description' => 'Invoice B3C5D117-0005',
            'destination' => NULL,
            'dispute' => NULL,
            'disputed' => false,
            'failure_code' => NULL,
            'failure_message' => NULL,
            'fraud_details' => 
            array (
            ),
            'invoice' => 'in_1IFopVJJYsXa4u2SATrYIPS3',
            'livemode' => false,
            'metadata' => 
            array (
            ),
            'on_behalf_of' => NULL,
            'order' => NULL,
            'outcome' => 
            array (
              'network_status' => 'approved_by_network',
              'reason' => NULL,
              'risk_level' => 'normal',
              'risk_score' => 53,
              'seller_message' => 'Payment complete.',
              'type' => 'authorized',
            ),
            'paid' => true,
            'payment_intent' => 'pi_1IFopWJJYsXa4u2SrTN4caYb',
            'payment_method' => 'pm_1IFopUJJYsXa4u2SX6n2I0n5',
            'payment_method_details' => 
            array (
              'card' => 
              array (
                'brand' => 'visa',
                'checks' => 
                array (
                  'address_line1_check' => NULL,
                  'address_postal_code_check' => 'pass',
                  'cvc_check' => 'pass',
                ),
                'country' => 'US',
                'exp_month' => 3,
                'exp_year' => 2030,
                'fingerprint' => 'r5AXBoNgpukUr3h0',
                'funding' => 'credit',
                'installments' => NULL,
                'last4' => '4242',
                'network' => 'visa',
                'three_d_secure' => NULL,
                'wallet' => NULL,
              ),
              'type' => 'card',
            ),
            'receipt_email' => NULL,
            'receipt_number' => NULL,
            'receipt_url' => 'https://pay.stripe.com/receipts/acct_1FmxdMJJYsXa4u2S/ch_1IFopWJJYsXa4u2Sxb2Jm0ug/rcpt_IrXvBGeBmVjnzAbl8gpAn2BwFvjJk78',
            'refunded' => false,
            'refunds' => 
            array (
              'object' => 'list',
              'data' => 
              array (
              ),
              'has_more' => false,
              'total_count' => 0,
              'url' => '/v1/charges/ch_1IFopWJJYsXa4u2Sxb2Jm0ug/refunds',
            ),
            'review' => NULL,
            'shipping' => NULL,
            'source' => NULL,
            'source_transfer' => NULL,
            'statement_descriptor' => NULL,
            'statement_descriptor_suffix' => NULL,
            'status' => 'succeeded',
            'transfer_data' => NULL,
            'transfer_group' => NULL,
          ),
        ),
        'has_more' => false,
        'total_count' => 1,
        'url' => '/v1/charges?payment_intent=pi_1IFopWJJYsXa4u2SrTN4caYb',
      ),
      'client_secret' => 'pi_1IFopWJJYsXa4u2SrTN4caYb_secret_6B5MAiBWs4efsSTe6YLmjVaio',
      'confirmation_method' => 'automatic',
      'created' => 1612133742,
      'currency' => 'usd',
      'customer' => 'cus_Ir9o6ATvKpAr65',
      'description' => 'Invoice B3C5D117-0005',
      'invoice' => 'in_1IFopVJJYsXa4u2SATrYIPS3',
      'last_payment_error' => NULL,
      'livemode' => false,
      'metadata' => 
      array (
      ),
      'next_action' => NULL,
      'on_behalf_of' => NULL,
      'payment_method' => 'pm_1IFopUJJYsXa4u2SX6n2I0n5',
      'payment_method_options' => 
      array (
        'card' => 
        array (
          'installments' => NULL,
          'network' => NULL,
          'request_three_d_secure' => 'automatic',
        ),
      ),
      'payment_method_types' => 
      array (
        0 => 'card',
      ),
      'receipt_email' => NULL,
      'review' => NULL,
      'setup_future_usage' => 'off_session',
      'shipping' => NULL,
      'source' => NULL,
      'statement_descriptor' => NULL,
      'statement_descriptor_suffix' => NULL,
      'status' => 'succeeded',
      'transfer_data' => NULL,
      'transfer_group' => NULL,
    ),
  ),
  'livemode' => false,
  'pending_webhooks' => 1,
  'request' => 
  array (
    'id' => 'req_RXHDClQbxmbztL',
    'idempotency_key' => NULL,
  ),
  'type' => 'payment_intent.succeeded',
)  

{
  "id": "evt_1IFUAIJJYsXa4u2SXCy9kmHf",
  "object": "event",
  "account": "acct_1FmxdMJJYsXa4u2S",
  "api_version": "2020-08-27",
  "created": 1612054304,
  "data": {
    "object": {
      "id": "pi_1IFUAFJJYsXa4u2SXOj0g7cj",
      "object": "payment_intent",
      "amount": 1001,
      "amount_capturable": 0,
      "amount_received": 1001,
      "application": "ca_Il7ro20ve0WNT1KhxA1SGVpoZ4Fro6RU",
      "application_fee_amount": 40,
      "canceled_at": null,
      "cancellation_reason": null,
      "capture_method": "automatic",
      "charges": {
        "object": "list",
        "data": [
          {
            "id": "ch_1IFUAGJJYsXa4u2S8azlstU2",
            "object": "charge",
            "amount": 1001,
            "amount_captured": 1001,
            "amount_refunded": 0,
            "application": "ca_Il7ro20ve0WNT1KhxA1SGVpoZ4Fro6RU",
            "application_fee": "fee_1IFUAGJJYsXa4u2S6P8pDOj8",
            "application_fee_amount": 40,
            "balance_transaction": "txn_1IFUAGJJYsXa4u2SSNqYK1IV",
            "billing_details": {
              "address": {
                "city": null,
                "country": "CA",
                "line1": null,
                "line2": null,
                "postal_code": "V3H 0B1",
                "state": null
              },
              "email": "webhooktest1@gmail.com",
              "name": "WebhookTestOne",
              "phone": null
            },
            "calculated_statement_descriptor": "APP PAYMENT",
            "captured": true,
            "created": 1612054304,
            "currency": "usd",
            "customer": "cus_Ir9o6ATvKpAr65",
            "description": "Invoice B3C5D117-0002",
            "destination": null,
            "dispute": null,
            "disputed": false,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {
            },
            "invoice": "in_1IFUAFJJYsXa4u2S7SAvX8hA",
            "livemode": false,
            "metadata": {
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
              "network_status": "approved_by_network",
              "reason": null,
              "risk_level": "normal",
              "risk_score": 60,
              "seller_message": "Payment complete.",
              "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_1IFUAFJJYsXa4u2SXOj0g7cj",
            "payment_method": "pm_1IFUAEJJYsXa4u2SLyAf3VaS",
            "payment_method_details": {
              "card": {
                "brand": "visa",
                "checks": {
                  "address_line1_check": null,
                  "address_postal_code_check": "pass",
                  "cvc_check": "pass"
                },
                "country": "US",
                "exp_month": 2,
                "exp_year": 2040,
                "fingerprint": "r5AXBoNgpukUr3h0",
                "funding": "credit",
                "installments": null,
                "last4": "4242",
                "network": "visa",
                "three_d_secure": null,
                "wallet": null
              },
              "type": "card"
            },
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/acct_1FmxdMJJYsXa4u2S/ch_1IFUAGJJYsXa4u2S8azlstU2/rcpt_IrCZegl0kq0Bg5TfGxLkhYbLECvVk9t",
            "refunded": false,
            "refunds": {
              "object": "list",
              "data": [
              ],
              "has_more": false,
              "total_count": 0,
              "url": "/v1/charges/ch_1IFUAGJJYsXa4u2S8azlstU2/refunds"
            },
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer_data": null,
            "transfer_group": null
          }
        ],
        "has_more": false,
        "total_count": 1,
        "url": "/v1/charges?payment_intent=pi_1IFUAFJJYsXa4u2SXOj0g7cj"
      },
      "client_secret": "pi_1IFUAFJJYsXa4u2SXOj0g7cj_secret_DYtVeg7lGXDzeESywhKHEaXPb",
      "confirmation_method": "automatic",
      "created": 1612054303,
      "currency": "usd",
      "customer": "cus_Ir9o6ATvKpAr65",
      "description": "Invoice B3C5D117-0002",
      "invoice": "in_1IFUAFJJYsXa4u2S7SAvX8hA",
      "last_payment_error": null,
      "livemode": false,
      "metadata": {
      },
      "next_action": null,
      "on_behalf_of": null,
      "payment_method": "pm_1IFUAEJJYsXa4u2SLyAf3VaS",
      "payment_method_options": {
        "card": {
          "installments": null,
          "network": null,
          "request_three_d_secure": "automatic"
        }
      },
      "payment_method_types": [
        "card"
      ],
      "receipt_email": null,
      "review": null,
      "setup_future_usage": "off_session",
      "shipping": null,
      "source": null,
      "statement_descriptor": null,
      "statement_descriptor_suffix": null,
      "status": "succeeded",
      "transfer_data": null,
      "transfer_group": null
    }
  },
  "livemode": false,
  "pending_webhooks": 1,
  "request": {
    "id": "req_yL013iibL0GF7T",
    "idempotency_key": null
  },
  "type": "payment_intent.succeeded"
}
*/
    
}