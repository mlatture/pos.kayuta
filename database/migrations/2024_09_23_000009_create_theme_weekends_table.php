<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('theme_weekends', static function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->string('sub_title', 255)->nullable();
            $table->string('inner_title', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('image', 255)->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theme_weekends');
    }
};
