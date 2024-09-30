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
        Schema::create('oauth_personal_access_clients', static function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('client_id');
            $table->timestamps();

            // Index for client_id
            $table->index('client_id', 'oauth_personal_access_clients_client_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('oauth_personal_access_clients');
    }
};
