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
       Schema::create('guest_submission_media', function (Blueprint $t) {
      $t->id();
      $t->unsignedBigInteger('submission_id');
      $t->string('filename',255)->nullable();
      $t->string('original_url',500)->nullable();
      $t->string('thumbnail_url',500)->nullable();
      $t->string('mime_type',100)->nullable();
      $t->unsignedBigInteger('filesize')->nullable();
      $t->timestamp('created_at')->useCurrent();

    //   $t->foreign('submission_id')->references('id')->on('guest_submissions')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guest_submission_media');
    }
};
