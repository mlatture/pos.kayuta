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
        $tableName = 'gift_cards';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'amount')) {
                    $table->double('amount')->default(0);
                }
                if (!Schema::hasColumn($tableName, 'modified_by')) {
                    $table->string('modified_by')->nullable();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->double('amount')->default(0);
                $table->string('modified_by')->nullable();
                $table->timestamps();
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
        Schema::table('gift_cards', static function (Blueprint $table) {
            $table->dropColumn('discount');
            $table->dropColumn('discount_type');
            $table->dropColumn('start_date');
            $table->dropColumn('limit');
        });
    }
};
