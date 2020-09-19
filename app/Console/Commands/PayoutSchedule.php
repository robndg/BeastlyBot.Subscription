<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\User;
use App\Subscription;
use App\StripeConnect;
use App\StripeHelper;
use App\DiscordStore;
use App\PaidOutInvoice;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PayoutSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payout:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find and issue payouts via Subscription DB';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $subscriptions_eligable = Subscription::whereNull('latest_paid_out_invoice_id')->orWhere('latest_paid_out_invoice_id', '!=', 'latest_invoice_id')->where('disputed_invoice_id', NULL)->where('latest_invoice_amount', '>', 0)->where('latest_invoice_paid_at', '<=', Carbon::now()->subDays(15))->get();
        
        Log::info($subscriptions_eligable);
        
        foreach ($subscriptions_eligable as $sub_eligable) {

            Log::info($sub_eligable);

            $stripe_connect = StripeConnect::find($sub_eligable->stripe_connect_id);

            \Stripe\Stripe::setApiKey(env('STRIPE_CLIENT_SECRET'));
            try {
                // Retrieve expressAccount
                $expressAccount = \Stripe\Account::retrieve(
                    $stripe_connect->express_id,
                    []
                );
                // We check if their Stripe account is from the US
                if($expressAccount->country == 'US'){
                    // We create the transfer for the payout amount
                    try {
                        
                        // Update DB first
                        $sub_eligable->latest_paid_out_invoice_id = $sub_eligable->latest_invoice_id;
                        $sub_eligable->save();

                        // Send payment
                        \Stripe\Transfer::create([
                            'amount' => $sub_eligable->latest_invoice_amount,
                            'currency' => 'usd',
                            'destination' => $stripe_connect->express_id,
                            'transfer_group' => $sub_eligable->id,
                        ]);

                        // Add payout entry
                        PaidOutInvoice::create([
                            'sub_id' => $sub_eligable->id,
                            'amount' => $sub_eligable->latest_invoice_amount,
                            'connection_type' => $sub_eligable->connection_type,
                            'connection_id' => $sub_eligable->connection_id,
                            'store_id' => $sub_eligable->store_id,
                        ]);


                    }catch (ApiErrorException $e) {
                        if (env('APP_DEBUG')) Log::error($e);
                        // Failed to Transfer
                    }

                }
            }catch (ApiErrorException $e) {
                if (env('APP_DEBUG')) Log::error($e);
                // Failed to get Stripe Express
            }


        }

        return 0;
    }
}