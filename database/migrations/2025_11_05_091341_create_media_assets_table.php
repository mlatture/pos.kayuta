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
       Schema::create('media_assets', function (Blueprint $t) {
      $t->id();
      $t->unsignedBigInteger('batch_id');
      $t->enum('type',['photo','video']);
      $t->string('original_url',500)->nullable();
      $t->string('proxy_url',500)->nullable();
      $t->string('thumbnail_url',500)->nullable();
      $t->string('filename',255)->nullable();
      $t->unsignedBigInteger('filesize')->nullable();
      $t->string('mime_type',100)->nullable();
      $t->json('exif_data')->nullable();
      $t->decimal('gps_latitude',10,8)->nullable();
      $t->decimal('gps_longitude',11,8)->nullable();
      $t->timestamp('shot_at')->nullable();
      $t->text('ai_analysis_summary')->nullable();
      $t->timestamps();

      $t->index(['batch_id','type'],'idx_batch_type');
      $t->index(['gps_latitude','gps_longitude'],'idx_gps');
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
        Schema::dropIfExists('media_assets');
    }
};
