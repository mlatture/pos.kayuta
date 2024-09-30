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
        Schema::create('hear_about_us', static function (Blueprint $table) {
            $table->id();
            $table->string('comment', 100)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('userid')->nullable();
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
        Schema::dropIfExists('hear_about_us');
    }
};
