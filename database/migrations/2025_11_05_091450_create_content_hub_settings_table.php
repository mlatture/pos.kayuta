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
        Schema::create('content_hub_settings', function (Blueprint $t) {
      $t->id(); // always 1
      $t->boolean('is_enabled')->default(false);
      $t->string('ai_service_provider',50)->default('claude');
      $t->text('ai_api_credentials')->nullable(); // encrypted JSON via model
      $t->integer('default_publish_delay_minutes')->default(5);
      $t->integer('auto_publish_after_approvals')->default(0);
      $t->boolean('face_blur_enabled')->default(true);
      $t->boolean('profanity_filter_enabled')->default(true);
      $t->boolean('guest_uploads_enabled')->default(true);
      $t->integer('max_media_per_batch')->default(20);
      $t->json('settings_json')->nullable();
      $t->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_hub_settings');
    }
};
