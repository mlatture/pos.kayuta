<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('oauth_access_tokens', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedInteger('client_id');
            $table->string('name', 191)->nullable();
            $table->text('scopes')->nullable();
            $table->tinyInteger('revoked');
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();

            $table->index('user_id', 'oauth_access_tokens_user_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('oauth_access_tokens');
    }
};
