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
        $tableName = 'search_functions';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'key')) {
                    $table->string('key', 150)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'url')) {
                    $table->string('url', 250)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'visible_for')) {
                    $table->string('visible_for', 191)->default('admin');
                }

                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }

                if (!Schema::hasColumn($tableName, 'updated_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('key', 150)->nullable();
                $table->string('url', 250)->nullable();
                $table->string('visible_for', 191)->default('admin');
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
        Schema::dropIfExists('search_functions');
    }
};
