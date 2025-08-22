<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_channel_id');
            $table->unsignedBigInteger('booking_id');     // points to final bookings table (existing app)
            $table->decimal('fee_total', 10, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['booking_channel_id', 'booking_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channel_bookings');
    }
}
