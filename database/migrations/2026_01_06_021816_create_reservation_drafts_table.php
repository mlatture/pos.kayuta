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
            Schema::create('reservation_drafts', function (Blueprint $table) {
                $table->id();
                $table->uuid('draft_id')->unique();
                $table->json('cart_data');
                $table->decimal('subtotal', 10, 2)->default(0);
                $table->decimal('discount_total', 10, 2)->default(0);
                $table->decimal('estimated_tax', 10, 2)->default(0);
                $table->decimal('platform_fee_total', 10, 2)->default(0);
                $table->decimal('grand_total', 10, 2)->default(0);
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
        Schema::dropIfExists('reservation_drafts');
    }
};
