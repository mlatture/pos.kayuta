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
    public function up(): void
    {
        $tableName = 'pos_payments';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'x_ref_num')) {
                    $table->string('x_ref_num')->nullable();
                }
            });
        } else {
            Schema::table($tableName, static function (Blueprint $table) {
                $table->string('x_ref_num')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('pos_payments', static function (Blueprint $table) {
            $table->dropColumn('x_ref_num');

        });
    }
};
