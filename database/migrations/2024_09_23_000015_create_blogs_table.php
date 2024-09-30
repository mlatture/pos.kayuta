<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blogs', static function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->string('slug', 255)->nullable();
            $table->longText('description');
            $table->string('image', 255)->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
