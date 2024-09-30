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
        Schema::create('chattings', static function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->boolean('sent_by_customer')->default(0);
            $table->boolean('sent_by_seller')->default(0);
            $table->boolean('sent_by_admin')->nullable();
            $table->boolean('sent_by_delivery_man')->nullable();
            $table->boolean('seen_by_customer')->default(1);
            $table->boolean('seen_by_seller')->default(1);
            $table->boolean('seen_by_admin')->nullable();
            $table->boolean('seen_by_delivery_man')->nullable();
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('delivery_man_id')->nullable();
            $table->unsignedBigInteger('shop_id')->nullable();
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
        Schema::dropIfExists('chattings');
    }
};
