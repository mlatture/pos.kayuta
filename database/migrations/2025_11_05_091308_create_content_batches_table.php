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
      Schema::create('content_batches', function (Blueprint $t) {
      $t->id();
      $t->unsignedBigInteger('creator_id');
      $t->string('title')->nullable();
      $t->text('seed_prompt')->nullable();
      $t->enum('status',['draft','generating','ready','publishing','published','failed'])->default('draft');
      $t->unsignedBigInteger('event_id')->nullable();
      $t->integer('total_media_count')->default(0);
      $t->decimal('ai_generation_cost',8,4)->default(0);
      $t->timestamps();
      $t->softDeletes();

      $t->index(['status']);
      $t->index(['creator_id','created_at'],'idx_creator_date');
    //   $t->foreign('creator_id')->references('id')->on('admins');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_batches');
    }
};
