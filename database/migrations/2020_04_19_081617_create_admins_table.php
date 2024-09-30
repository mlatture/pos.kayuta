<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admins', static function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->nullable();
            $table->string('phone', 25)->nullable();
            $table->bigInteger('admin_role_id')->default(2);
            $table->string('image', 30)->default('def.png');
            $table->string('email', 80)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 80);
            $table->rememberToken();
            $table->timestamps();
            $table->boolean('status')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
