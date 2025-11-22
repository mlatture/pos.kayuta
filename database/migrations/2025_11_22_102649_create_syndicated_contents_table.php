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
        Schema::create('syndicated_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('idea_id')->index();
            $table->string('channel')->default('rvcamping'); 
            // e.g. 'rvcamping', 'partner_blog', etc.

            $table->string('title');
            $table->text('body_md');          // markdown body
            $table->json('meta')->nullable(); // e.g. park name, urls, etc.
            $table->enum('status', ['pending','processed','deleted'])->default('pending');
            $table->timestamps();

            $table->index(['tenant_id', 'channel', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('syndicated_contents');
    }
};
