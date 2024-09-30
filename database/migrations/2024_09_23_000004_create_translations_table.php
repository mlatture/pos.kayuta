<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('translations', static function (Blueprint $table) {
            $table->id();
            $table->string('translationable_type', 191);
            $table->unsignedBigInteger('translationable_id');
            $table->string('locale', 191);
            $table->string('key', 191)->nullable();
            $table->text('value')->nullable();
            $table->timestamps();

            $table->index('translationable_id');
            $table->index('locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
