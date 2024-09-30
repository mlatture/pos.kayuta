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
        Schema::create('oauth_refresh_tokens', static function (Blueprint $table) {
            $table->id();
            $table->string('access_token_id', 100);
            $table->tinyInteger('revoked');
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();

            // Index for access_token_id
            $table->index('access_token_id', 'oauth_refresh_tokens_access_token_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('oauth_refresh_tokens');
    }
};
