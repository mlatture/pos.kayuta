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
        Schema::table('gift_cards', static function (Blueprint $table) {
            $table->double('amount')->default(0);
            $table->string('modified_by')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('gift_cards', static function (Blueprint $table) {
            $table->dropColumn('discount');
            $table->dropColumn('discount_type');
            $table->dropColumn('start_date');
            $table->dropColumn('limit');
        });
    }
};
