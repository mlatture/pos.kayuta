<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('channel_cart_id');
            $table->nullableMorphs('bookable');           // bookable_type, bookable_id
            $table->string('sku')->nullable();
            $table->unsignedInteger('qty')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->unsignedBigInteger('booking_channel_id')->nullable();
            $table->json('metadata')->nullable();         // e.g., dates/options
            $table->timestamps();

            $table->index(['channel_cart_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
}
