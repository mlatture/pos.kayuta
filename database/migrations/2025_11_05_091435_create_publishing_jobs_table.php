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
         Schema::create('publishing_jobs', function (Blueprint $t) {
      $t->id();
      $t->unsignedBigInteger('draft_id');
      $t->enum('channel',['website','facebook','instagram','tiktok','youtube_shorts','google_business','pinterest']);
      $t->timestamp('scheduled_at')->nullable();
      $t->integer('attempts')->default(0);
      $t->integer('max_attempts')->default(3);
      $t->enum('status',['pending','processing','completed','failed'])->default('pending');
      $t->json('result')->nullable();
      $t->text('error_message')->nullable();
      $t->timestamps();

      $t->index(['scheduled_at','status'],'idx_scheduled_status');
      $t->index(['draft_id','status'],'idx_draft_status');
    //   $t->foreign('draft_id')->references('id')->on('channel_drafts')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('publishing_jobs');
    }
};
