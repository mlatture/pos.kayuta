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
        $tableName = 'personal_access_tokens';

        if (Schema::hasTable($tableName)) {
            // If the table exists, modify it
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'tokenable_type')) {
                    $table->string('tokenable_type', 191)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'tokenable_id')) {
                    $table->unsignedBigInteger('tokenable_id')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'name')) {
                    $table->string('name', 191)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'token')) {
                    $table->string('token', 64)->unique('personal_access_tokens_token_unique')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'abilities')) {
                    $table->text('abilities')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'last_used_at')) {
                    $table->timestamp('last_used_at')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }

                if (!Schema::hasColumn($tableName, 'updated_at')) {
                    $table->timestamps();
                }

                // Index on tokenable_type and tokenable_id
                if (!Schema::hasIndex('personal_access_tokens_tokenable_type_tokenable_id_index')) {
                    $table->index(['tokenable_type', 'tokenable_id'], 'personal_access_tokens_tokenable_type_tokenable_id_index');
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('tokenable_type', 191)->nullable();
                $table->unsignedBigInteger('tokenable_id')->nullable();
                $table->string('name', 191)->nullable();
                $table->string('token', 64)->unique('personal_access_tokens_token_unique')->nullable();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamps();

                // Index on tokenable_type and tokenable_id
                $table->index(['tokenable_type', 'tokenable_id'], 'personal_access_tokens_tokenable_type_tokenable_id_index');
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
        Schema::dropIfExists('personal_access_tokens');
    }
};
