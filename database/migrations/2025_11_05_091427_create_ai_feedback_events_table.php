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
        Schema::create('ai_feedback_events', function (Blueprint $t) {
      $t->id();
      $t->unsignedBigInteger('batch_id');
      $t->text('feedback_notes')->nullable();
      $t->boolean('regeneration_requested')->default(false);
      $t->boolean('training_data_saved')->default(false);
      $t->json('before_content')->nullable();
      $t->json('after_content')->nullable();
      $t->unsignedBigInteger('created_by')->nullable();
      $t->timestamp('created_at')->useCurrent();

      $t->index(['batch_id','training_data_saved'],'idx_batch_training');
    //   $t->foreign('batch_id')->references('id')->on('content_batches')->onDelete('cascade');
    //   $t->foreign('created_by')->references('id')->on('admins');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ai_feedback_events');
    }
};
