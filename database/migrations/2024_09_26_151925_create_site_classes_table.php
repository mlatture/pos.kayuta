<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_classes', static function (Blueprint $table) {
            $table->id();
            $table->string('siteclass');
            $table->boolean('showriglength')->default(0);
            $table->boolean('showhookup')->default(0);
            $table->boolean('showrigtype')->default(0);
            $table->string('tax')->nullable();
            $table->integer('orderby')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_classes');
    }
};
