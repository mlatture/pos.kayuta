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
        $tableName = 'directions';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'title')) {
                    $table->string('title', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'description')) {
                    $table->longText('description')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'latitude')) {
                    $table->string('latitude', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'longitude')) {
                    $table->string('longitude', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'pdf')) {
                    $table->string('pdf', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->boolean('status')->default(0);
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('title', 255)->nullable();
                $table->longText('description')->nullable();
                $table->string('latitude', 255)->nullable();
                $table->string('longitude', 255)->nullable();
                $table->string('pdf', 255)->nullable();
                $table->boolean('status')->default(0);
                $table->timestamps();
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
        Schema::dropIfExists('directions');
    }
};
