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
        Schema::create('campground_maps', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->string('image', 255)->nullable();
            $table->string('pdf', 255)->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps(); // This adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('campground_maps');
    }
};
