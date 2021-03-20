<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('store_type')->default(1); // discord
            $table->integer('store_id')->unique();
            $table->string('store_image')->nullable();
            $table->string('store_name')->nullable();
            $table->string('url_slug')->unique();
            
            $table->longText('description')->nullable();
            $table->longText('about')->nullable();
           
            $table->boolean('members_only')->default(false);

            $table->longText('welcome_message')->nullable();
            $table->integer('welcome_message_settings')->default(1); // 0 no // 1 public // 2 message

            $table->boolean('refunds_enabled')->default(true);
            $table->integer('refunds_terms')->default(0); // 0 anytime // 1 owner discretion
            $table->integer('refunds_days')->default(7);

            $table->boolean('referrals_enabled')->default(false);
            $table->boolean('recurring_referrals')->default(true);
            $table->double('referral_percent_fee')->default(10);

            $table->boolean('cancel_subscriptions_on_exit')->default(false);
            $table->boolean('disable_public_downgrades')->default(false);

            $table->longText('terms_of_service')->nullable();

            $table->boolean('premium')->default(false);

            $table->boolean('remove_network')->default(false);
            $table->string('main_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->boolean('show_beastly')->default(true);
            $table->string('eyes_color')->default('#b0e0e6');
            
            $table->boolean('allow_featured')->default(false);

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
        Schema::dropIfExists('store_settings');
    }
}
