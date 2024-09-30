<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('transactions', static function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->nullable();
            $table->string('payment_for', 100)->nullable();
            $table->bigInteger('payer_id')->nullable();
            $table->bigInteger('payment_receiver_id')->nullable();
            $table->string('paid_by', 15)->nullable();
            $table->string('paid_to', 15)->nullable();
            $table->string('payment_method', 15)->nullable();
            $table->string('payment_status', 10)->default('success');
            $table->timestamps();
            $table->double('amount', 8, 2)->default(0.00);
            $table->string('transaction_type', 191)->nullable();
            $table->unsignedBigInteger('order_details_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
