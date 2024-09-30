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
        Schema::create('virtual_tour_of_bears', static function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->text('video_link');
            $table->longText('description')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps(); // created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_tour_of_bears');
    }
};
