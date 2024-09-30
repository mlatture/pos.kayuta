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
    public function up(): void
    {
        Schema::table('pos_payments', static function (Blueprint $table) {
                $table->string('payment_method')->nullable();
                $table->string('payment_acc_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('pos_payments', static function (Blueprint $table) {
            $table->dropColumn('payment_method');
            $table->dropColumn('payment_acc_number');

        });
    }
};
