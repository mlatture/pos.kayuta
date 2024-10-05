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
        $tableName = 'contacts';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'name')) {
                    $table->string('name', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'email')) {
                    $table->string('email', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'mobile_number')) {
                    $table->string('mobile_number', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'subject')) {
                    $table->string('subject', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'message')) {
                    $table->text('message')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'seen')) {
                    $table->boolean('seen')->default(0);
                }
                if (!Schema::hasColumn($tableName, 'feedback')) {
                    $table->string('feedback', 191)->default('0');
                }
                if (!Schema::hasColumn($tableName, 'reply')) {
                    $table->longText('reply')->nullable();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('name', 191)->nullable();
                $table->string('email', 191)->nullable();
                $table->string('mobile_number', 191)->nullable();
                $table->string('subject', 191)->nullable();
                $table->text('message')->nullable();
                $table->boolean('seen')->default(0);
                $table->string('feedback', 191)->default('0');
                $table->longText('reply')->nullable();
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
        Schema::dropIfExists('contacts');
    }
};
