<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $tableName = 'trailer_toung_weights';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn($tableName, 'title')) {
                    $table->string('title', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'description')) {
                    $table->longText('description')->nullable();
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
                $table->longText('description')->nullable();
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('trailer_toung_weights');
    }
};
