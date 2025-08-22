<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelSystemLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_system_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->uuid('trace_id')->nullable();
            $table->string('transaction_type'); // key_create | key_rotate | key_revoke | api_auth
            $table->string('status')->nullable(); // success | failed
            $table->string('ip')->nullable();
            $table->string('ua', 512)->nullable();
            $table->json('payload_snippet')->nullable();
            $table->timestamps();

            $table->index(['transaction_type','status']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channel_system_logs');
    }
}
