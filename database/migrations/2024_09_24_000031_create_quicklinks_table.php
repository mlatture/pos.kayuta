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
        $tableName = 'quicklinks';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'url')) {
                    $table->string('url', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'description')) {
                    $table->text('description')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'order')) {
                    $table->integer('order')->default(0);
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
                $table->string('url', 255)->nullable();
                $table->text('description')->nullable();
                $table->integer('order')->default(0);
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
        Schema::dropIfExists('quicklinks');
    }
};
