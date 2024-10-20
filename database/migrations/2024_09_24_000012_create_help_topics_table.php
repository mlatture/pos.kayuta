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
        $tableName = 'help_topics';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'question')) {
                    $table->text('question')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'answer')) {
                    $table->text('answer')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'ranking')) {
                    $table->integer('ranking')->default(1);
                }
                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->tinyInteger('status')->default(1);
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->text('question')->nullable();
                $table->text('answer')->nullable();
                $table->integer('ranking')->default(1);
                $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('help_topics');
    }
};
