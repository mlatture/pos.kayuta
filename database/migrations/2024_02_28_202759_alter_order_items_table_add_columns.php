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
        $tableName = 'order_items';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'tax')) {
                    $table->double('tax')->default(0)->after('product_id');
                }
                if (!Schema::hasColumn($tableName, 'discount')) {
                    $table->double('discount')->default(0)->after('tax');
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->double('tax')->default(0)->after('product_id');
                $table->double('discount')->default(0)->after('tax');
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
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('tax');
            $table->dropColumn('discount');
        });
    }
};
