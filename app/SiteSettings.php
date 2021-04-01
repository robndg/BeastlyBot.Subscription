<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteSettings extends Model
{
    //
    protected $fillable = ['type', 'category', 'name', 'title', 'description', 'description', 'code', 'val_integer', 'val_double', 'val_text', 'val_bool', 'val_string', 'val_array', 'position', 'role', 'enabled', 'metadata'];

    protected $casts = [
        'val_array' => 'array',
        'metadata' => 'array',
    ];

}

/*

            $table->id();
            // site
            $table->integer('type')->default(1); // 1 Product for Store Owners
            $table->integer('category')->default('Premium'); ex) Premium
            $table->string('name')->nullable();
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->string('code')->nullable(); // ex) Stripe code id

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

*/