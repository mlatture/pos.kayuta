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
        $tableName = 'cart_reservations';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'number_of_guests')) {
                    $table->integer('number_of_guests')->nullable();
                }
            });
        } else {
            Schema::table($tableName, static function (Blueprint $table) {
                $table->integer('number_of_guests')->nullable();
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
        Schema::table('cart_reservations', static function (Blueprint $table) {
            $table->dropColumn('number_of_guests');
        });
    }
};
