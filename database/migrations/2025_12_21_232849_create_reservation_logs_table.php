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
        Schema::create('reservation_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('event_type'); // e.g., 'created', 'check_in', 'check_out', 'print', 'email'
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->text('comment')->nullable();
            $table->string('ip_address')->nullable();
            
            // Timestamps (created_at is the main log time)
            $table->timestamps();

            $table->index('reservation_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservation_logs');
    }
};
