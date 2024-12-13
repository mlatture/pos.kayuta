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
        Schema::create('upsell_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number');
            $table->string('cashier');
            $table->unsignedBigInteger('upsell_text_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('upsell_text_id')->references('id')->on('upsell_text')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('upsell_orders');
    }
};
