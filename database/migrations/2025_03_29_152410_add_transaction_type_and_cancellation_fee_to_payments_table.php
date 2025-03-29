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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('transaction_type')->nullable()->after('payment');
            $table->decimal('refunded_amount', 8, 2)->nullable()->after('transaction_type');
            $table->decimal('cancellation_fee', 8, 2)->nullable()->after('transaction_type');
    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['transaction_type','refunded_amount' ,'cancellation_fee']);
        });
    }
};
