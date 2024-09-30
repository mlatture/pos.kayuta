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
        Schema::create('oauth_clients', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name', 191);
            $table->string('secret', 100);
            $table->text('redirect');
            $table->tinyInteger('personal_access_client');
            $table->tinyInteger('password_client');
            $table->tinyInteger('revoked');
            $table->string('provider', 191)->nullable();
            $table->timestamps();

            // Index for user_id
            $table->index('user_id', 'oauth_clients_user_id_index');
        });
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
