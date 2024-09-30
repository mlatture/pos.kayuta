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
        Schema::create('virtual_tour_of_sites', static function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->text('video_link')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('status')->default(false);
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
        Schema::dropIfExists('virtual_tour_of_sites');
    }
};
