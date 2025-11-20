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
        Schema::create('content_ideas', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('tenant_id')->index();
    $table->unsignedBigInteger('category_id')->index();
    $table->string('title');
    $table->text('summary')->nullable();
    $table->unsignedTinyInteger('rank')->default(1);
    $table->enum('status', ['draft', 'approved', 'rejected'])->default('draft');
    $table->json('ai_inputs')->nullable();
    $table->timestamps();

    $table->index(['tenant_id', 'category_id', 'status']);
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_ideas');
    }
};
