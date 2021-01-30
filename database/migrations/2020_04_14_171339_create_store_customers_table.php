<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_customers', function (Blueprint $table) {
            //$table->id();
            $table->uuid('id')->unique();
            $table->bigInteger('user_id')->nullable(); // can get discord id through this
            $table->bigInteger('discord_store_id'); // for searching if create customer in store for connect
            $table->string('customer_stripe_id')->nullable(); // 
            $table->string('customer_paypal_id')->nullable();
            $table->string('customer_cur')->default('usd');
            
            $table->longText('stripe_token')->nullable(); // 
            $table->longText('paypal_token')->nullable(); // 
           
            $table->json('stripe_metadata')->nullable(); // can store and update user prefs
            $table->json('paypal_metadata')->nullable(); // can store and update user prefs

            $table->string('referal_code')->nullable();

            $table->integer('enabled')->default(1); // can use to ban stripe/paypal payments (0) or if shop owner stripe account changes (10)
            $table->string('ip_address')->nullable();
            $table->json('metadata')->nullable(); // our own metadata
            
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
        Schema::dropIfExists('store_customers');
    }
}
