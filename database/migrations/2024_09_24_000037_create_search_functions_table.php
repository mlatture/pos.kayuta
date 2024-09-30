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
        Schema::create('search_functions', static function (Blueprint $table) {
            $table->id();
            $table->string('key', 150)->nullable();
            $table->string('url', 250)->nullable();
            $table->string('visible_for', 191)->default('admin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('search_functions');
    }
};
