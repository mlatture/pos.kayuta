<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $tableName = 'admins';

//        admins schema

        if (Schema::hasTable($tableName)) {

            Schema::table($tableName, static function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn($tableName, 'name')) {
                    $table->string('name', 80)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'phone')) {
                    $table->string('phone', 25)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'admin_role_id')) {
                    $table->bigInteger('admin_role_id')->default(2);
                }
                if (!Schema::hasColumn($tableName, 'image')) {
                    $table->string('image', 30)->default('def.png');
                }
                if (!Schema::hasColumn($tableName, 'email')) {
                    $table->string('email', 80)->unique();
                }
                if (!Schema::hasColumn($tableName, 'email_verified_at')) {
                    $table->timestamp('email_verified_at')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'password')) {
                    $table->string('password', 80);
                }
                if (!Schema::hasColumn($tableName, 'remember_token')) {
                    $table->rememberToken();
                }
                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->boolean('status')->default(1);
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, static function (Blueprint $table) {
                $table->id();
                $table->string('name', 80)->nullable();
                $table->string('phone', 25)->nullable();
                $table->bigInteger('admin_role_id')->default(2);
                $table->string('image', 30)->default('def.png');
                $table->string('email', 80)->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password', 80);
                $table->rememberToken();
                $table->boolean('status')->default(1);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
