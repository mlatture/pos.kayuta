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
        $tableName = 'kayuta_videos';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'video')) {
                    $table->string('video', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->tinyInteger('status')->default(0);
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('video', 255)->nullable();
                $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('kayuta_videos');
    }
};