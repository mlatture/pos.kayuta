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
        Schema::create('whitelist_tables', static function (Blueprint $table) {
            $table->id();
            $table->string('table_name')->unique();
            $table->integer('read_permission_level')->nullable();
            $table->integer('update_permission_level')->nullable();
            $table->integer('delete_permission_level')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('whitelist_tables');
    }
};
