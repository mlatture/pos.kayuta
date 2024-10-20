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
    public function up(): void
    {
        $tableName = 'oauth_clients';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'name')) {
                    $table->string('name', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'secret')) {
                    $table->string('secret', 100)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'redirect')) {
                    $table->text('redirect')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'personal_access_client')) {
                    $table->tinyInteger('personal_access_client')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'password_client')) {
                    $table->tinyInteger('password_client')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'revoked')) {
                    $table->tinyInteger('revoked')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'provider')) {
                    $table->string('provider', 191)->nullable();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('name', 191)->nullable();
                $table->string('secret', 100)->nullable();
                $table->text('redirect')->nullable();
                $table->tinyInteger('personal_access_client')->nullable();
                $table->tinyInteger('password_client')->nullable();
                $table->tinyInteger('revoked')->nullable();
                $table->string('provider', 191)->nullable();
                $table->timestamps();

                // Index for user_id
                $table->index('user_id', 'oauth_clients_user_id_index');
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
        Schema::dropIfExists('oauth_clients');
    }
};
