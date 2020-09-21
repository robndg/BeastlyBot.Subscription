<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\WebhookClient\Models\WebhookCall;

class DisputeUpdated implements ShouldQueue
{
    public function handle(WebhookCall $webhookCall)
    {
        
    }

    // {
    //     "created": 1326853478,
    //     "livemode": false,
    //     "id": "evt_00000000000000",
    //     "type": "charge.dispute.updated",
    //     "object": "event",
    //     "request": null,
    //     "pending_webhooks": 1,
    //     "api_version": "2019-08-14",
    //     "data": {
    //       "object": {
    //         "id": "dp_00000000000000",
    //         "object": "dispute",
    //         "amount": 1000,
    //         "balance_transactions": [
    //         ],
    //         "charge": "ch_00000000000000",
    //         "created": 1600648588,
    //         "currency": "usd",
    //         "evidence": {
    //           "access_activity_log": null,
    //           "billing_address": null,
    //           "cancellation_policy": null,
    //           "cancellation_policy_disclosure": null,
    //           "cancellation_rebuttal": null,
    //           "customer_communication": null,
    //           "customer_email_address": null,
    //           "customer_name": null,
    //           "customer_purchase_ip": null,
    //           "customer_signature": null,
    //           "duplicate_charge_documentation": null,
    //           "duplicate_charge_explanation": null,
    //           "duplicate_charge_id": null,
    //           "product_description": null,
    //           "receipt": null,
    //           "refund_policy": null,
    //           "refund_policy_disclosure": null,
    //           "refund_refusal_explanation": null,
    //           "service_date": null,
    //           "service_documentation": null,
    //           "shipping_address": null,
    //           "shipping_carrier": null,
    //           "shipping_date": null,
    //           "shipping_documentation": null,
    //           "shipping_tracking_number": null,
    //           "uncategorized_file": null,
    //           "uncategorized_text": "Here is some evidence"
    //         },
    //         "evidence_details": {
    //           "due_by": 1602374399,
    //           "has_evidence": false,
    //           "past_due": false,
    //           "submission_count": 0
    //         },
    //         "is_charge_refundable": true,
    //         "livemode": false,
    //         "metadata": {
    //         },
    //         "payment_intent": "pi_00000000000000",
    //         "reason": "general",
    //         "status": "under_review"
    //       },
    //       "previous_attributes": {
    //         "evidence": {
    //           "access_activity_log": null,
    //           "billing_address": null,
    //           "cancellation_policy": null,
    //           "cancellation_policy_disclosure": null,
    //           "cancellation_rebuttal": null,
    //           "customer_communication": null,
    //           "customer_email_address": null,
    //           "customer_name": null,
    //           "customer_purchase_ip": null,
    //           "customer_signature": null,
    //           "duplicate_charge_documentation": null,
    //           "duplicate_charge_explanation": null,
    //           "duplicate_charge_id": null,
    //           "product_description": null,
    //           "receipt": null,
    //           "refund_policy": null,
    //           "refund_policy_disclosure": null,
    //           "refund_refusal_explanation": null,
    //           "service_date": null,
    //           "service_documentation": null,
    //           "shipping_address": null,
    //           "shipping_carrier": null,
    //           "shipping_date": null,
    //           "shipping_documentation": null,
    //           "shipping_tracking_number": null,
    //           "uncategorized_file": null,
    //           "uncategorized_text": "Old uncategorized text"
    //         }
    //       }
    //     }
    //   }

}