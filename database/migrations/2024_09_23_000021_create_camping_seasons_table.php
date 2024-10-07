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
        $tableName = 'camping_seasons';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'opening_day')) {
                    $table->timestamp('opening_day')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'closing_day')) {
                    $table->timestamp('closing_day')->nullable();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id(); // Auto-incrementing ID
                $table->timestamp('opening_day')->nullable();
                $table->timestamp('closing_day')->nullable();
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
        Schema::dropIfExists('camping_seasons');
    }
};
