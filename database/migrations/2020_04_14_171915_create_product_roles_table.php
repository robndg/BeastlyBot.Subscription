<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('discord_store_id');
            $table->longText('role_id');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->integer('access')->default(0);
            $table->string('start_date')->nullable(); // date time
            $table->string('end_date')->nullable(); // date time
            $table->integer('max_sales')->nullable();
            $table->integer('active')->default(0);
            //$table->string('UUID')->unique();
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
        Schema::dropIfExists('product_roles');
    }
}
