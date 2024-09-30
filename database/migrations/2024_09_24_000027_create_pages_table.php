<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pages', static function (Blueprint $table) {
            $table->id();
            $table->string('metatitle', 255)->nullable();
            $table->string('metadescription', 255)->nullable();
            $table->string('canonicalurl', 255)->nullable();
            $table->string('opengraphimage', 255)->nullable();
            $table->string('opengraphtitle', 255)->nullable();
            $table->string('opengraphdescription', 255)->nullable();
            $table->text('schema_code_pasting')->nullable();
            $table->string('title', 255)->nullable();
            $table->string('slug', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('attachment', 100)->nullable();
            $table->string('image', 255)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
