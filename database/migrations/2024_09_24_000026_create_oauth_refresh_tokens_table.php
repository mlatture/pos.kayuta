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
        $tableName = 'oauth_refresh_tokens';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'access_token_id')) {
                    $table->string('access_token_id', 100)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'revoked')) {
                    $table->tinyInteger('revoked')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'expires_at')) {
                    $table->dateTime('expires_at')->nullable();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('access_token_id', 100)->nullable();
                $table->tinyInteger('revoked')->nullable();
                $table->dateTime('expires_at')->nullable();
                $table->timestamps();

                // Index for access_token_id
                $table->index('access_token_id', 'oauth_refresh_tokens_access_token_id_index');
            });
        }
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
