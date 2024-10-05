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
        $tableName = 'cache_locks';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'key')) {
                    $table->string('key', 255)->primary();
                }
                if (!Schema::hasColumn($tableName, 'owner')) {
                    $table->string('owner', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'expiration')) {
                    $table->integer('expiration')->nullable();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->string('key', 255)->primary();
                $table->string('owner', 255)->nullable();
                $table->integer('expiration')->nullable();
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
        Schema::dropIfExists('cache_locks');
    }
};
