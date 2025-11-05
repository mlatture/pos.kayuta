<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('ai_tone_profiles', function (Blueprint $t) {
      $t->id();
      $t->enum('channel',['website','facebook','instagram','tiktok','youtube_shorts','google_business','pinterest']);
      $t->json('tone_settings')->nullable();
      $t->json('learned_examples')->nullable();
      $t->json('hashtag_preferences')->nullable();
      $t->text('content_guidelines')->nullable();
      $t->timestamps();

      $t->unique(['channel'],'unique_channel_tone');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ai_tone_profiles');
    }
};
