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

class PaymentIntentSucceeded implements ShouldQueue
{
    public function handle(WebhookCall $webhookCall) // Set DB status to 1, then fire role and set to 2
    {
        Log::info("PaymentIntentSucceeded Webhook Call");
        Log::info($webhookCall->payload);
    }

/*
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