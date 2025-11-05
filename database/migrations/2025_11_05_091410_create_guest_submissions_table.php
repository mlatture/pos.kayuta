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
         Schema::create('guest_submissions', function (Blueprint $t) {
      $t->id();
      $t->string('submission_token',100)->unique();
      $t->string('submitter_name',255)->nullable();
      $t->string('submitter_email',255)->nullable();
      $t->text('caption')->nullable();
      $t->boolean('consent_given')->default(false);
      $t->enum('moderation_status',['pending','approved','declined'])->default('pending');
      $t->unsignedBigInteger('moderated_by')->nullable();
      $t->timestamp('moderated_at')->nullable();
      $t->text('moderation_notes')->nullable();
      $t->timestamp('created_at')->useCurrent();

      $t->index(['moderation_status','created_at'],'idx_moderation_queue');
    //   $t->foreign('moderated_by')->references('id')->on('admins');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guest_submissions');
    }
};
