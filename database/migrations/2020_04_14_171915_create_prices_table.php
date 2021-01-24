<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('product_id');
           // $table->string('UUID')->unique();
            
            $table->string('stripe_price_id')->nullable();
            $table->string('paypal_price_id')->nullable();
            
            $table->integer('price')->nullable();
            $table->string('cur')->default('usd');
            $table->string('interval')->default('monthly');

            $table->integer('assigned_to')->nullable();
            $table->string('start_date')->nullable(); // date time
            $table->string('end_date')->nullable(); // date time
            $table->integer('max_sales')->nullable();

            $table->integer('discount')->nullable();
            $table->string('discount_end')->nullable(); // date time
            $table->integer('discount_max')->nullable();

            $table->integer('status')->default(1);

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
        Schema::dropIfExists('prices');
    }
}
