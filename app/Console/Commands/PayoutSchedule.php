<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\User;
use App\Subscription;
use App\StripeConnect;
use App\StripeHelper;
use App\DiscordStore;
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
        
        foreach ($subscriptions_eligable as $sub_eligable) {

            $stripe_connect = StripeConnect::find($sub_eligable->stripe_connect_id);

            try {
                // Retrieve expressAccount
                $expressAccount = $stripe->accounts->retrieve(
                    $stripe_connect->express_id,
                    []
                );
                // We check if their Stripe account is from the US
                if($expressAccount->country == 'US'){
                    // We create the transfer for the payout amount
                    try {
                        $stripe->transfers->create([
                            'amount' => $sub_eligable->amount_to_pay_out * 100,
                            'currency' => 'usd',
                            'destination' => $stripe_connect->express_id,
                            'transfer_group' => $sub_eligable->id,
                        ]);
                        
                        // Success
                        $sub_eligable->amount_to_pay_out -= $sub_eligable->amount_to_pay_out;
                        $sub_eligable->latest_paid_out_invoice_id = $sub_eligable->latest_invoice_id;
                        $sub_eligable->save();


                    }catch (ApiErrorException $e) {
                        // Failed to Transfer
                    }

                }
            }catch (ApiErrorException $e) {
                // Failed to get Stripe Express
            }


        }

        return 0;
    }
}