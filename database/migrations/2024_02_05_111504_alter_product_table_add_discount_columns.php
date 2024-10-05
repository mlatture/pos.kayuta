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
        $tableName = 'products';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'type')) {
                    $table->string('type')->after('quantity')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'discount_type')) {
                    $table->string('discount_type')->after('type')->comment('percentage, fixed')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'discount')) {
                    $table->double('discount')->after('discount_type')->default(0);
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('type')->after('quantity')->nullable();
                $table->string('discount_type')->nullable()->comment('percentage, fixed');
                $table->double('discount')->after('discount_type')->default(0);
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
            $table->dropColumn('type');
            $table->dropColumn('discount_type');
            $table->dropColumn('discount');
        });
    }
};
