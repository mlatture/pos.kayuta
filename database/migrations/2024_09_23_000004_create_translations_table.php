<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        $tableName = 'translations';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                // Check for existing columns and add if missing
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn($tableName, 'translationable_type')) {
                    $table->string('translationable_type', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'translationable_id')) {
                    $table->unsignedBigInteger('translationable_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'locale')) {
                    $table->string('locale', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'key')) {
                    $table->string('key', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'value')) {
                    $table->text('value')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }

                // Check for existing indexes
                if (!Schema::hasIndex('translations_translationable_id_index')) {
                    $table->index('translationable_id')->nullable();
                }
                if (!Schema::hasIndex('translations_locale_index')) {
                    $table->index('locale')->nullable();
                }
            });
        } else {
            Schema::create('translations', static function (Blueprint $table) {
                $table->id();
                $table->string('translationable_type', 191)->nullable();
                $table->unsignedBigInteger('translationable_id')->nullable();
                $table->string('locale', 191)->nullable();
                $table->string('key', 191)->nullable();
                $table->text('value')->nullable();
                $table->timestamps();

                $table->index('translationable_id')->nullable();
                $table->index('locale')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
