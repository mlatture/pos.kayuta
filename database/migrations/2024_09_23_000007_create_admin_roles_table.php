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
        $tableName = 'admin_roles';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn($tableName, 'name')) {
                    $table->string('name', 30)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'module_access')) {
                    $table->string('module_access', 250)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->boolean('status')->default(1);
                }
                if (!Schema::hasColumn($tableName, 'is_pos')) {
                    $table->boolean('is_pos')->default(1);
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('name', 30)->nullable();
                $table->string('module_access', 250)->nullable();
                $table->boolean('status')->default(1);
                $table->boolean('is_pos')->default(1);
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
        Schema::dropIfExists('admin_roles');
    }
};
