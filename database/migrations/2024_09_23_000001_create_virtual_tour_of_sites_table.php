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
        $tableName = 'virtual_tour_of_sites';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn($tableName, 'title')) {
                    $table->string('title', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'video_link')) {
                    $table->text('video_link')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'description')) {
                    $table->longText('description')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->boolean('status')->default(false);
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('title', 255)->nullable();
                $table->text('video_link')->nullable();
                $table->longText('description')->nullable();
                $table->boolean('status')->default(false);
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
        Schema::dropIfExists('virtual_tour_of_sites');
    }
};
