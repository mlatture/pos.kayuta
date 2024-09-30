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
        Schema::create('infos', static function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->longText('description')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('show_in_details')->default(0);
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
        Schema::dropIfExists('infos');
    }
};
