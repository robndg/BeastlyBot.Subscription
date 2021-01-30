<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary(); // can check so no trouble, send this as success url or session

            $table->integer('connection_type')->default(1); // 1 stripe, 2 paypal
            $table->longText('session_id')->nullable(); // Should be unique, might need change if paypal 

            $table->string('sub_id')->nullable(); // Should be unique, will add in webhook
            $table->bigInteger('user_id');
            $table->bigInteger('owner_id')->nullable(); // need if stripe change
            $table->bigInteger('store_id')->nullable(); // links to ex) discord_store ID (null if for us)
            $table->longText('store_customer_id')->nullable(); //uuid

            $table->string('product_id')->nullable(); // links to ex) product_roles ID uuid
            $table->string('price_id')->nullable(); // links to ex) product

            $table->string('first_invoice_id')->nullable();
            $table->integer('first_invoice_price')->nullable();
            $table->string('first_invoice_paid_at')->nullable(); 
            $table->integer('next_invoice_price')->nullable();
            $table->string('latest_invoice_id')->nullable();
            $table->integer('latest_invoice_amount')->nullable();

            $table->integer('app_fee')->default(4); // level for 

            $table->integer('status')->default(0); // 0 not paid, maybe 1 good, 2 overdue, 3 cancelled, 4 disputed
            $table->integer('visible')->default(1); // can use to hide on site if not paid or premium plan etc
            $table->json('metadata')->nullable();

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
        Schema::dropIfExists('subscriptions');
    }
}
