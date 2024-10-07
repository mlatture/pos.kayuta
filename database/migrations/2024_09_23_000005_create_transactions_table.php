<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        $tableName = 'transactions';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn($tableName, 'order_id')) {
                    $table->bigInteger('order_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'payment_for')) {
                    $table->string('payment_for', 100)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'payer_id')) {
                    $table->bigInteger('payer_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'payment_receiver_id')) {
                    $table->bigInteger('payment_receiver_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'paid_by')) {
                    $table->string('paid_by', 15)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'paid_to')) {
                    $table->string('paid_to', 15)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'payment_method')) {
                    $table->string('payment_method', 15)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'payment_status')) {
                    $table->string('payment_status', 10)->default('success');
                }
                if (!Schema::hasColumn($tableName, 'amount')) {
                    $table->double('amount', 8, 2)->default(0.00);
                }
                if (!Schema::hasColumn($tableName, 'transaction_type')) {
                    $table->string('transaction_type', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'order_details_id')) {
                    $table->unsignedBigInteger('order_details_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->bigInteger('order_id')->nullable();
                $table->string('payment_for', 100)->nullable();
                $table->bigInteger('payer_id')->nullable();
                $table->bigInteger('payment_receiver_id')->nullable();
                $table->string('paid_by', 15)->nullable();
                $table->string('paid_to', 15)->nullable();
                $table->string('payment_method', 15)->nullable();
                $table->string('payment_status', 10)->default('success');
                $table->double('amount', 8, 2)->default(0.00);
                $table->string('transaction_type', 191)->nullable();
                $table->unsignedBigInteger('order_details_id')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
