<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            // site
            $table->integer('type')->default(1); // 1 Product for Store Owners
            $table->string('category')->default('Premium'); // ex) Premium, 
            $table->string('name')->nullable();
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->string('code')->nullable(); // Stripe code

            // vals
            $table->integer('val_integer')->nullable();
            $table->double('val_double')->nullable();
            $table->longText('val_text')->nullable();
            $table->boolean('val_bool')->nullable(); // ex) used for sale, ex) recurring, cancel if bot leaves etc.
            $table->string('val_string')->nullable(); // ex) discount amount string
            $table->json('val_array')->nullable(); // ex) sale ends, max, etc

            $table->integer('position')->nullable(); // position
            $table->integer('role')->default(0); // anyone
            $table->integer('enabled')->default(1); // 1 enabled

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
        Schema::dropIfExists('site_settings');
    }
}
