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
        if (Schema::hasColumn('system_logs', 'action')) {
            Schema::table('system_logs', function (Blueprint $table) {
                $table->dropColumn('action');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('system_logs', 'action')) {
            Schema::table('system_logs', function (Blueprint $table) {
                $table->string('action')->nullable()->after('status');
            });
        }
    }
};
