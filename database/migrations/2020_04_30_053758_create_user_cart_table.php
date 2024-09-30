<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('admin_cart', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id');
            $table->foreignId('product_id');
            $table->unsignedInteger('quantity');

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_cart');
    }
};
