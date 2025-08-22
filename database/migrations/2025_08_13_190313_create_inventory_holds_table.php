<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryHoldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_holds', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->nullable();
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->string('resource_type')->nullable(); // e.g., Room, Ticket, etc.
            $table->unsignedBigInteger('cart_item_id')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamp('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['sku', 'resource_id', 'cart_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_holds');
    }
}
