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
        $tableName = 'products';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'quantity')) {
                    $table->string('quantity')->change()->nullable();
                }
            });
        } else {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('quantity')->change()->nullable();
            });
        }
    }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public
        function down()
        {
            Schema::table('products', function (Blueprint $table) {
                $table->integer('quantity')->change();
            });
        }
    };
