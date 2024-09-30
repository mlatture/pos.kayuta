<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('reviews', static function (Blueprint $table) {
            $table->id();
            $table->mediumText('comment')->nullable();
            $table->string('attachment', 191)->nullable();
            $table->integer('rating')->default(0);
            $table->integer('status')->default(1);
            $table->boolean('is_saved')->default(0);
            $table->string('type', 191)->default('product');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('delivery_man_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
