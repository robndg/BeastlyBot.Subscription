<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutoMigratesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::create('auto_migrations', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->default(1); // Upgrade
            $table->integer('store_id')->nullable();

            $table->json('stripe_subscriptions')->nullable();
            $table->json('prices_meta')->nullable();
            
            $table->integer('products_created')->default(0); // 1 yes, 2 error
            $table->json('products_created_array')->nullable(); // to show
            $table->json('products_errors_array')->nullable(); // ie. json(product)
            $table->integer('prices_created')->default(0); // 1 yes, 2 error
            $table->json('prices_created_array')->nullable(); // to show
            $table->json('prices_errors_array')->nullable(); // ie. json(price)
            
            $table->integer('subscriptions_created')->default(0); // 1 yes, 2 error
            $table->json('subscriptions_created_array')->nullable(); // for stripe process
            $table->json('subscriptions_errors_array')->nullable(); // ie. json(user)
            
            $table->integer('step')->default(0); // 1 got subscriptions, // 2 kicks bot, maybe cancel subs // 3 products creating (3.5 error check array) // 4 prices creating (with assigned diff users if diff price) (4.5 error array) // 5 subscriptions table created (5.5 error check array) // 6 subscriptions cancelled, create (6.5 error) // 7 confirming // 8 complete // 9 user agrees to terms // 10 done, redirect to dash or store
           
            $table->longText('notes')->nullable(); // just for us
            $table->json('metadata')->nullable(); // just for us
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
        Schema::dropIfExists('auto_migrates');
    }
}
