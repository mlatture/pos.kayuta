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
        $tableName = 'seasons';

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('name', 255)->nullable();
                $table->longText('description')->nullable();
                $table->string('image', 255)->nullable();
                $table->text('sub_heading')->nullable();
                $table->string('year', 255)->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'name')) {
                    $table->string('name', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'description')) {
                    $table->longText('description')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'image')) {
                    $table->string('image', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'sub_heading')) {
                    $table->text('sub_heading')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'year')) {
                    $table->string('year', 255)->nullable();
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
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};
