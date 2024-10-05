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
        $tableName = 'whitelist_tables';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'table_name')) {
                    $table->string('table_name')->unique();
                }
                if (!Schema::hasColumn($tableName, 'read_permission_level')) {
                    $table->integer('read_permission_level')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'update_permission_level')) {
                    $table->integer('update_permission_level')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'delete_permission_level')) {
                    $table->integer('delete_permission_level')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('table_name')->unique();
                $table->integer('read_permission_level')->nullable();
                $table->integer('update_permission_level')->nullable();
                $table->integer('delete_permission_level')->nullable();
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
        Schema::dropIfExists('whitelist_tables');
    }
};
