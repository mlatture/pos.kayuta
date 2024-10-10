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
        $tableName = 'order_items';
        if (Schema::hasTable($tableName)) {
            if (Schema::hasColumn($tableName, 'price')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->decimal('price', 14, 4)->nullable()->change();
                });
            } else {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->decimal('price', 14, 4)->nullable();
                });
            }
        }

        $tableName = 'payments';
        if (Schema::hasTable($tableName)) {
            if (Schema::hasColumn($tableName, 'amount')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->decimal('amount', 14, 4)->nullable()->change();
                });
            } else {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->decimal('amount', 14, 4)->nullable();
                });
            }
        }

        $tableName = 'products';
        if (Schema::hasTable($tableName)) {
            if (Schema::hasColumn($tableName, 'price')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->decimal('price', 14, 2)->nullable()->change();
                });
            } else {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->decimal('price', 14, 2)->nullable();
                });
            }
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
            //
        });
        Schema::table('payments', function (Blueprint $table) {
            //
        });
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
