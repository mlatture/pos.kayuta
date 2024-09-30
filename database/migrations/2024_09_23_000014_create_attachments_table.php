<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attachments', static function (Blueprint $table) {
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

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
