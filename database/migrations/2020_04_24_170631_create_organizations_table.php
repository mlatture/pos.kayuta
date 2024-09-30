<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('organizations', static function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('address_1', 191)->nullable();
            $table->string('address_2', 191)->nullable();
            $table->string('city', 191)->nullable();
            $table->string('state', 191)->nullable();
            $table->string('zip', 191)->nullable();
            $table->string('country', 191)->default('USA');
            $table->string('status', 191)->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
