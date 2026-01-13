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
        Schema::table('reservation_drafts', function (Blueprint $table) {
            $table->string('coupon_code')->nullable()->after('platform_fee_total');
            $table->text('discount_reason')->nullable()->after('grand_total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservation_drafts', function (Blueprint $table) {
            $table->dropColumn(['coupon_code', 'discount_reason']);
        });
    }
};
