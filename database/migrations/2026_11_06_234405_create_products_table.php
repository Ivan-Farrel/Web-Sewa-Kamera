<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category'); // DSLR / Mirrorless
            $table->integer('price_8h');
            $table->integer('price_12h');
            $table->integer('price_24h');
            $table->integer('price_2d');
            $table->integer('overtime_per_hour');
            $table->string('status')->default('available'); // available / rented / maintenance
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
