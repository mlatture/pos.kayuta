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
        $tableName = 'campsite_pics';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'title')) {
                    $table->string('title', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'image')) {
                    $table->string('image', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->boolean('status')->default(false);
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id(); // Auto-incrementing ID
                $table->string('title', 255)->nullable();
                $table->string('image', 255)->nullable();
                $table->boolean('status')->default(false);
                $table->timestamps(); // This adds created_at and updated_at columns
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
        Schema::dropIfExists('campsite_pics');
    }
};
