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
           Schema::create('social_connections', function (Blueprint $t) {
      $t->id();
      $t->enum('channel',['facebook','instagram','tiktok','youtube','google_business','pinterest']);
      $t->string('account_name',255)->nullable();
      $t->string('account_id',255)->nullable();
      $t->text('access_token')->nullable();   // encrypted via model
      $t->text('refresh_token')->nullable();  // encrypted via model
      $t->timestamp('token_expires_at')->nullable();
      $t->boolean('is_active')->default(true);
      $t->timestamp('last_health_check')->nullable();
      $t->enum('health_status',['healthy','warning','error'])->default('healthy');
      $t->json('connection_metadata')->nullable();
      $t->timestamps();

      $t->unique(['channel']); // single-tenant: one account per channel (adjust if needed)
      $t->index(['is_active'],'idx_active');
    });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('social_connections');
    }
};
