<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $tableName = 'theme_weekends';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn($tableName, 'title')) {
                    $table->string('title', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'sub_title')) {
                    $table->string('sub_title', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'inner_title')) {
                    $table->string('inner_title', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'image')) {
                    $table->string('image', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->tinyInteger('status')->default(0);
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('title', 255)->nullable();
                $table->string('sub_title', 255)->nullable();
                $table->string('inner_title', 255)->nullable();
                $table->text('description')->nullable();
                $table->string('image', 255)->nullable();
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('theme_weekends');
    }
};
