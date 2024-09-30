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
        Schema::create('drafts', static function (Blueprint $table) {
            $table->id();
            $table->string('content', 255);
            $table->string('title', 255);
            $table->string('image', 255)->nullable();
            $table->boolean('image_position')->default(0);
            $table->unsignedBigInteger('content_id');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('content_id')->references('id')->on('contents');

            // Index for content_id
            $table->index('content_id', 'drafts_content_id_foreign');
        });
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
