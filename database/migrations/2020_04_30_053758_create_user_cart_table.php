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
    public function up()
    {
        // Check if the table already exists to avoid trying to recreate it
        if (!Schema::hasTable('admin_cart')) {
            Schema::create('admin_cart', function (Blueprint $table) {
                $table->id();
                $table->foreignId('admin_id');
                $table->foreignId('product_id');
                $table->unsignedInteger('quantity');

                $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_cart');
    }
};
