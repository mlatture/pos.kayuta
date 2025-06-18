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
        Schema::create('bills', function (Blueprint $table) {
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();

            $table->string('kwh_used');
            $table->decimal('rate', 8, 4);
            $table->decimal('total_cost', 10, 2);

            $table->json('reading_dates')->nullable();

            $table->boolean('auto_email')->default(false);

            $table->timestamps();

            $table->foreign('reservation_id')->references('id')->on('reservations')->nullOnDelete();
            $table->foreign('customer_id')->references('id')->on('users')->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bills');
    }
};
