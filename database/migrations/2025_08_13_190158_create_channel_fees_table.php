<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_channel_id');
            $table->string('name')->nullable();
            $table->enum('type', ['flat', 'percent']);
            $table->decimal('amount', 10, 2);
            $table->boolean('pass_to_customer')->default(false); // internal attribution by default
            $table->boolean('is_active')->default(true);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_to')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('booking_channel_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channel_fees');
    }
}
