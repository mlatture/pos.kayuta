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
        $tableName = 'contents';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'title')) {
                    $table->string('title', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'slug')) {
                    $table->string('slug', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'content')) {
                    $table->text('content')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'image')) {
                    $table->string('image', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'image_position')) {
                    $table->boolean('image_position')->default(0);
                }
                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->boolean('status')->nullable();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('title', 255)->nullable();
                $table->string('slug', 255)->nullable();
                $table->text('content')->nullable();
                $table->string('image', 255)->nullable();
                $table->boolean('image_position')->default(0);
                $table->boolean('status')->nullable();
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
        Schema::dropIfExists('contents');
    }
};
