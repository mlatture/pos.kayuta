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
        $tableName = 'menu_items';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'title')) {
                    $table->string('title', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'url')) {
                    $table->string('url', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'target')) {
                    $table->string('target', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'order')) {
                    $table->integer('order')->default(0);
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('title', 255)->nullable();
                $table->string('url', 255)->nullable();
                $table->string('target', 255)->nullable();
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
        Schema::dropIfExists('menu_items');
    }
};
