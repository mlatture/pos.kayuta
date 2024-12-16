<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAddonBookings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the table already exists before creating it
        if (!Schema::hasTable('user_addon_bookings')) {
            Schema::create('user_addon_bookings', function (Blueprint $table) {
                $table->id(); // Primary key
                $table->unsignedBigInteger('addon_id'); // Unsigned foreign key to addons table
                $table->unsignedBigInteger('user_id'); // Unsigned foreign key to users table
                $table->date('start_date'); // Booking start date
                $table->date('end_date'); // Booking end date
                $table->timestamps(); // Timestamps for created_at and updated_at

                // Add foreign key constraints (optional)
                $table->foreign('addon_id')->references('id')->on('addons')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('user_addon_bookings');
    }
}
