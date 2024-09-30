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
        Schema::create('admin_roles', static function (Blueprint $table) {
            $table->id();
            $table->string('name', 30)->nullable();
            $table->string('module_access', 250)->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('is_pos')->default(1);
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
        Schema::dropIfExists('admin_roles');
    }
};
