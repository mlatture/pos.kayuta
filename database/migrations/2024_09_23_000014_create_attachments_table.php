<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $tableName = 'attachments';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn($tableName, 'title')) {
                    $table->string('title', 150)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'type')) {
                    $table->string('type', 50)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'attachmenttype')) {
                    $table->string('attachmenttype', 100)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'attachment')) {
                    $table->string('attachment', 100)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->boolean('status')->default(1);
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('title', 150)->nullable();
                $table->text('description')->nullable();
                $table->string('type', 50)->nullable();
                $table->string('attachmenttype', 100)->nullable();
                $table->string('attachment', 100)->nullable();
                $table->boolean('status')->default(1);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
