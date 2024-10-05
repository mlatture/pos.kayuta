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
        $tableName = 'notifications';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'title')) {
                    $table->string('title', 100)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'description')) {
                    $table->string('description', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'notification_count')) {
                    $table->integer('notification_count')->default(0);
                }
                if (!Schema::hasColumn($tableName, 'image')) {
                    $table->string('image', 50)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->tinyInteger('status')->default(1);
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('title', 100)->nullable();
                $table->string('description', 191)->nullable();
                $table->integer('notification_count')->default(0);
                $table->string('image', 50)->nullable();
                $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('notifications');
    }
};
