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
       Schema::create('channel_drafts', function (Blueprint $t) {
      $t->id();
      $t->unsignedBigInteger('batch_id');
      $t->enum('channel',['website','facebook','instagram','tiktok','youtube_shorts','google_business','pinterest']);
      $t->longText('draft_content')->nullable();
      $t->text('system_notes')->nullable();
      $t->integer('version')->default(1);
      $t->enum('status',['generated','edited','scheduled','published','failed'])->default('generated');
      $t->timestamp('scheduled_at')->nullable();
      $t->timestamp('published_at')->nullable();
      $t->json('publish_result')->nullable();
      $t->timestamps();

      $t->index(['batch_id','channel'],'idx_batch_channel');
      $t->index(['status','scheduled_at'],'idx_status_scheduled');
    //   $t->foreign('batch_id')->references('id')->on('content_batches')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channel_drafts');
    }
};
