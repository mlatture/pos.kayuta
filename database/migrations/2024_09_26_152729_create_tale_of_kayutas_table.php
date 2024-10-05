<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableName = 'tale_of_kayutas';

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->longText('description')->nullable();
                $table->boolean('status')->default(1);
                $table->timestamps();
            });
        } else {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'title')) {
                    $table->string('title')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'description')) {
                    $table->longText('description')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->boolean('status')->default(1);
                }

                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
                }

                if (!Schema::hasColumn($tableName, 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tale_of_kayutas');
    }
};
