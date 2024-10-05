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
        $tableName = 'dictionary_tables';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'table_name')) {
                    $table->string('table_name')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'field_name')) {
                    $table->string('field_name')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'display_name')) {
                    $table->string('display_name')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'order')) {
                    $table->integer('order')->default(0);
                }
                if (!Schema::hasColumn($tableName, 'viewable')) {
                    $table->boolean('viewable')->default(true);
                }
                if (!Schema::hasColumn($tableName, 'validation')) {
                    $table->string('validation')->default('not_required')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'visibility')) {
                    $table->enum('visibility', ['all', 'read_only', 'hidden'])->default('all');
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('table_name')->nullable(); // "All" can be used for global fields
                $table->string('field_name')->nullable();
                $table->string('display_name')->nullable();
                $table->text('description')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('viewable')->default(true);
                $table->string('validation')->default('not_required')->nullable();
                $table->enum('visibility', ['all', 'read_only', 'hidden'])->default('all');
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
        Schema::dropIfExists('dictionary_tables');
    }
};
