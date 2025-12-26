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
        Schema::table('additional_payments', function (Blueprint $table) {
            $table->string('register_id')->nullable()->after('created_by');
        });

        Schema::table('refunds', function (Blueprint $table) {
            $table->string('register_id')->nullable()->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('additional_payments', function (Blueprint $table) {
            $table->dropColumn('register_id');
        });

        Schema::table('refunds', function (Blueprint $table) {
            $table->dropColumn('register_id');
        });
    }
};
