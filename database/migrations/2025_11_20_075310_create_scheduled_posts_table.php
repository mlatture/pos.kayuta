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
       Schema::create('scheduled_posts', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('tenant_id')->index();
    $table->unsignedBigInteger('idea_id')->nullable()->index();
    $table->dateTimeTz('scheduled_at')->nullable();
    $table->json('platform_targets')->nullable(); // ['facebook','instagram',...]
    $table->json('post_payload')->nullable();     // per-platform captions, links, images
    $table->enum('status', ['draft', 'queued', 'posted', 'failed'])->default('draft');
    $table->string('hq_job_id')->nullable();      // HQ side reference if needed
    $table->timestamps();

    $table->index(['tenant_id', 'status', 'scheduled_at']);
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scheduled_posts');
    }
};
