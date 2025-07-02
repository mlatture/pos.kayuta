<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduled_payments', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_email');

            $table->date('payment_date');
            $table->enum('payment_type', ['ACH', 'Credit'])->default('Credit');

            $table->string('reference_key')->nullable()->comment('Used to trigger external payment gateway');
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->nullOnDelete();

            $table->boolean('reminder_sent')->default(false);
            $table->boolean('recurring')->default(false);
            $table->enum('frequency', ['None', 'Daily', 'Weekly', 'Monthly', 'Yearly'])->default('None');

            $table->enum('status', ['Pending', 'Completed', 'Declined – retrying', 'Declined – failed'])->default('Pending');

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
        Schema::dropIfExists('scheduled_payments');
    }
};
