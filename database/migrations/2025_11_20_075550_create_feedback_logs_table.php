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
       Schema::create('feedback_logs', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('tenant_id')->index();
    $table->enum('source', ['idea', 'post']);
    $table->unsignedBigInteger('source_id');
    $table->string('action');   // e.g. 'approve','replace','delete','edit'
    $table->integer('weight')->default(1);
    $table->json('context')->nullable();
    $table->timestamps();

    $table->index(['tenant_id', 'source', 'action']);
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feedback_logs');
    }
};
