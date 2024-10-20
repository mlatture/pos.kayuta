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
        $tableName = 'oauth_access_tokens';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'client_id')) {
                    $table->unsignedInteger('client_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'name')) {
                    $table->string('name', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'scopes')) {
                    $table->text('scopes')->nullable();
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
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedInteger('client_id')->nullable();
                $table->string('name', 191)->nullable();
                $table->text('scopes')->nullable();
                $table->tinyInteger('revoked')->nullable();
                $table->dateTime('expires_at')->nullable();
                $table->timestamps();

                $table->index('user_id', 'oauth_access_tokens_user_id_index');
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
        Schema::dropIfExists('oauth_access_tokens');
    }
};
