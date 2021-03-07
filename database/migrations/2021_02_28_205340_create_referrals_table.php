<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable(); // referer
            $table->integer('store_type')->default(1); // 1 discord
            $table->integer('store_id')->nullable();
            $table->string('referrer_customer_id');
            $table->string('purchaser_customer_id');
            $table->uuid('subscription_id')->nullable();
            $table->string('referral_code')->nullable(); // not null for now (should be discord ID)
            $table->integer('refund_amount')->nullable();
            $table->string('refund_sub_id')->nullable(); // which sub refunded this payout
            $table->string('refund_invoice_id')->nullable(); // rest done with cron
            $table->integer('paid')->nullable(); // should be refund_amount
            $table->integer('override')->default(0); // 1 to cancel
            $table->integer('count')->default(0); // recur count
            $table->longText('UUID')->nullable(); // subID
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referrals');
    }
}