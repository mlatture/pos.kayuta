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
        Schema::table('orders', function (Blueprint $table) {
            $table->bigInteger('gift_card_id')->after('admin_id')->nullable();
            $table->double('amount')->after('gift_card_id')->nullable();
            $table->double('gift_card_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('gift_card_id');
            $table->dropColumn('amount');
            $table->dropColumn('gift_card_amount');
        });
    }
};
