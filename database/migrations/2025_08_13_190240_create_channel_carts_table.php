<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_carts', function (Blueprint $table) {
            $table->id();
            $table->uuid('token')->unique();             // public cart token
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('booking_channel_id')->nullable();
            $table->enum('status', ['open', 'expired', 'converted'])->default('open');
            $table->char('currency', 3)->default('USD');
            $table->timestamp('expires_at')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('internal_fee_total', 10, 2)->default(0); // not exposed to guests
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'booking_channel_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channel_carts');
    }
}
