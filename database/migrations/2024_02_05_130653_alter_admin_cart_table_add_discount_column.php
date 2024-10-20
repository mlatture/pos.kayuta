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
        $tableName = 'admin_cart';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'discount')) {
                    $table->double('discount')->default(0);
                }
            });
        } else {
            Schema::table('admin_cart', function (Blueprint $table) {
                $table->double('discount')->default(0);
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
        Schema::table('admin_cart', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
    }
};
