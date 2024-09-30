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
        Schema::create('contacts', static function (Blueprint $table) {
            $table->id();
            $table->string('name', 191)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('mobile_number', 191);
            $table->string('subject', 191);
            $table->text('message');
            $table->boolean('seen')->default(0);
            $table->string('feedback', 191)->default('0');
            $table->longText('reply')->nullable();
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
        Schema::dropIfExists('contacts');
    }
};
