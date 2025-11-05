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
        Schema::create('draft_versions', function (Blueprint $t) {
      $t->id();
      $t->unsignedBigInteger('draft_id');
      $t->longText('content')->nullable();
      $t->text('change_notes')->nullable();
      $t->unsignedBigInteger('created_by')->nullable();
      $t->timestamp('created_at')->useCurrent();

      $t->index(['draft_id','created_at'],'idx_draft_date');
      $t->foreign('draft_id')->references('id')->on('channel_drafts')->onDelete('cascade');
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
        Schema::dropIfExists('draft_versions');
    }
};
