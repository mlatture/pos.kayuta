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
        Schema::create('seasonal_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('default_rate', 10, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->date('renewal_deadline')->nullable();
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->json('rate_tiers')->nullable(); 
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
        Schema::dropIfExists('seasonal_settings');
    }
};
