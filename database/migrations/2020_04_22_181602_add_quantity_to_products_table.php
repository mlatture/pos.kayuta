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
    public function up(): void
    {
        $tableName = 'products';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, static function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'quantity')) {
                    $table->integer('quantity')->after('price')->default(1);
                }
            });
        }
        else {
            Schema::table('products', static function (Blueprint $table) {
                $table->integer('quantity')->after('price')->default('1');
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
        Schema::table('products', static function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};
