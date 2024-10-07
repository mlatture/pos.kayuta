<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $tableName = 'organizations';

//        organizations schemaa
        if (Schema::hasTable($tableName)) {

            Schema::table($tableName, static function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn($tableName, 'name')) {
                    $table->string('name', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'address_1')) {
                    $table->string('address_1', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'address_2')) {
                    $table->string('address_2', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'city')) {
                    $table->string('city', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'state')) {
                    $table->string('state', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'zip')) {
                    $table->string('zip', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'country')) {
                    $table->string('country', 191)->default('USA');
                }
                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->string('status', 191)->default('Active');
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }
                if (!Schema::hasColumn($tableName, 'updated_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, static function (Blueprint $table) {
                $table->id();
                $table->string('name', 191)->nullable();
                $table->string('address_1', 191)->nullable();
                $table->string('address_2', 191)->nullable();
                $table->string('city', 191)->nullable();
                $table->string('state', 191)->nullable();
                $table->string('zip', 191)->nullable();
                $table->string('country', 191)->default('USA');
                $table->string('status', 191)->default('Active');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
