<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableName = 'submenu_items';

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->foreignId('menu_item_id')->constrained()->onDelete('cascade')->nullable();
                $table->string('title')->nullable();
                $table->string('url')->nullable();
                $table->string('target')->default('_self');
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        } else {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'menu_item_id')) {
                    $table->foreignId('menu_item_id')->constrained()->onDelete('cascade')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'title')) {
                    $table->string('title')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'url')) {
                    $table->string('url')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'target')) {
                    $table->string('target')->default('_self');
                }

                if (!Schema::hasColumn($tableName, 'order')) {
                    $table->integer('order')->default(0);
                }

                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
                }

                if (!Schema::hasColumn($tableName, 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submenu_items');
    }
};
