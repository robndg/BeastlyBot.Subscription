<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sub_id');
            $table->string('start_date');
            $table->string('period_end')->nullable();
            $table->longText('user_id')->nullable();
            $table->bigInteger('owner_id')->nullable();
            $table->string('stripe_account_id')->nullable();
            $table->string('guild_name')->nullable();
            $table->string('role_name')->nullable();
            $table->string('guild_id')->nullable();
            $table->string('role_id')->nullable();
            $table->boolean('refund_enabled')->nullable();
            $table->string('refund_days')->nullable();
            $table->string('refund_terms')->nullable();
            $table->longText('description')->nullable();
            $table->string('plan_id')->nullable();
            $table->string('amount')->nullable();
            $table->string('application_fee')->nullable();
            $table->boolean('decision')->default(false)->nullable();
            $table->boolean('issued')->nullable();
            $table->boolean('override')->default(false)->nullable();
            $table->bigInteger('refunder')->nullable();
            $table->boolean('kick')->default(false);
            $table->boolean('ban')->default(false);
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
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('refund_requests');
    }
}
