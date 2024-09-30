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
        Schema::create('sites', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->string('siteid');
            $table->string('sitename');
            $table->string('siteclass');
            $table->string('hookup');
            $table->boolean('availableonline')->default(1);
            $table->boolean('available')->default(1);
            $table->boolean('seasonal')->default(0);
            $table->integer('maxlength');
            $table->integer('minlength');
            $table->json('rigtypes')->nullable();
            $table->string('class');
            $table->string('coordinates');
            $table->string('attributes');
            $table->json('amenities')->nullable();
            $table->text('description')->nullable();
            $table->string('ratetier');
            $table->string('tax');
            $table->integer('minimumstay')->default(1);
            $table->string('sitesection');
            $table->string('youtube')->nullable();
            $table->string('vt_tour')->nullable();
            $table->string('embeddedvideo')->nullable();
            $table->dateTime('lastmeterreading')->nullable();
            $table->integer('orderby')->default(0);
            $table->dateTime('lastmodified')->nullable();
            $table->json('images')->nullable();
            $table->timestamps();
            $table->string('photo_360_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
