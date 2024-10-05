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
        $tableName = 'currencies';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'name')) {
                    $table->string('name', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'symbol')) {
                    $table->string('symbol', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'code')) {
                    $table->string('code', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'exchange_rate')) {
                    $table->string('exchange_rate', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->boolean('status')->default(0);
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('name', 191)->nullable();
                $table->string('symbol', 191)->nullable();
                $table->string('code', 191)->nullable();
                $table->string('exchange_rate', 191)->nullable();
                $table->boolean('status')->default(0);
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
        Schema::dropIfExists('currencies');
    }
};
