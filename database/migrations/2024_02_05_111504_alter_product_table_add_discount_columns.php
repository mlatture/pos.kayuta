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
        Schema::table('products', static function (Blueprint $table) {
            $table->string('type')->after('quantity')->nullable();
            $table->string('discount_type')->after('type')->comment('percentage, fixed')->nullable();
            $table->double('discount')->after('discount_type')->default(0);
        });
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
