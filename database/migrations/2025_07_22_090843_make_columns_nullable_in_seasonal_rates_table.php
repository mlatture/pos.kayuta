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
        Schema::table('seasonal_rates', function (Blueprint $table) {
            $table->decimal('rate_price', 8, 2)->nullable()->change();
            $table->decimal('deposit_amount', 8, 2)->nullable()->change();
            $table->decimal('early_pay_discount', 8, 2)->nullable()->change();
            $table->decimal('full_payment_discount', 8, 2)->nullable()->change();
            $table->foreignId('template_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seasonal_rates', function (Blueprint $table) {
            $table->decimal('rate_price', 8, 2)->default(0)->change();
            $table->decimal('deposit_amount', 8, 2)->default(0)->change();
            $table->decimal('early_pay_discount', 8, 2)->default(0)->change();
            $table->decimal('full_payment_discount', 8, 2)->default(0)->change();
            $table->foreignId('template_id')->constrained('document_templates')->nullable(false)->change();
        });
    }
};
