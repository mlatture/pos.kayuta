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
        Schema::create('dictionary_tables', static function (Blueprint $table) {
            $table->id();
            $table->string('table_name')->nullable(); // "All" can be used for global fields
            $table->string('field_name');
            $table->string('display_name')->nullable();
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('viewable')->default(true);
            $table->enum('visibility', ['all', 'read_only', 'hidden'])->default('all');
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
        Schema::dropIfExists('dictionary_tables');
    }
};
