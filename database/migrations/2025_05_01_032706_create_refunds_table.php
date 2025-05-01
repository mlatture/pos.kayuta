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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->string('cartid');
            $table->decimal('amount', 8, 2);
            $table->decimal('cancellation_fee');
            $table->unsignedBigInteger('reservations_id');
            $table->foreign('reservations_id')->references('id')->on('reservations');
            $table->string('reason')->nullable();
            $table->string('method');
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
        Schema::dropIfExists('refunds');
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'transaction_type')) {
                $table->dropColumn('transaction_type');
            }
            if (Schema::hasColumn('payments', 'refunded_amount')) {
                $table->dropColumn('refunded_amount');
            }
            if (Schema::hasColumn('payments', 'cancellation_fee')) {
                $table->dropColumn('cancellation_fee');
            }
        });
    
    }
};
