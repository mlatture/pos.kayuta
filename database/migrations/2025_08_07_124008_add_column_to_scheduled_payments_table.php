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
        Schema::table('scheduled_payments', function (Blueprint $table) {
            $table->decimal('paid_amount', 8, 2)->default(0.00)->after('amount')->comment('Amount that has been paid for this scheduled payment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scheduled_payments', function (Blueprint $table) {
            $table->dropColumn('paid_amount');
        });
    }
};
