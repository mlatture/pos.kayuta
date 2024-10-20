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
        $tableName = 'drafts';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'content')) {
                    $table->string('content', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'title')) {
                    $table->string('title', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'image')) {
                    $table->string('image', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'image_position')) {
                    $table->boolean('image_position')->default(0);
                }
                if (!Schema::hasColumn($tableName, 'content_id')) {
                    $table->unsignedBigInteger('content_id')->nullable();
                    // Foreign key constraint
                    $table->foreign('content_id')->references('id')->on('contents');
                    // Index for content_id
                    $table->index('content_id', 'drafts_content_id_foreign');
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('content', 255)->nullable();
                $table->string('title', 255)->nullable();
                $table->string('image', 255)->nullable();
                $table->boolean('image_position')->default(0);
                $table->unsignedBigInteger('content_id')->nullable();
                $table->timestamps();

                // Foreign key constraint
                $table->foreign('content_id')->references('id')->on('contents');

                // Index for content_id
                $table->index('content_id', 'drafts_content_id_foreign');
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
        Schema::dropIfExists('drafts');
    }
};
