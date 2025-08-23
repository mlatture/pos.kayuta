<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_channels', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();          // short code, e.g. "AFFIL"
            $table->string('name');
            $table->string('api_key')->unique();       // per-tenant/partner key
            $table->string('api_secret');              // consider hashing later
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
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
        Schema::dropIfExists('booking_channels');
    }
}
