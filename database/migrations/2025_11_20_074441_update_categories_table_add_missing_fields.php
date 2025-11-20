<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {

            if (!Schema::hasColumn('categories', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->index();
            }

            if (!Schema::hasColumn('categories', 'name')) {
                $table->string('name');
            }

            if (!Schema::hasColumn('categories', 'slug')) {
                $table->string('slug')->index();
            }

            if (!Schema::hasColumn('categories', 'template_prompt')) {
                $table->text('template_prompt')->nullable();
            }

            if (!Schema::hasColumn('categories', 'is_custom')) {
                $table->boolean('is_custom')->default(false);
            }

            if (!Schema::hasColumn('categories', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {

            if (Schema::hasColumn('categories', 'tenant_id')) {
                $table->dropColumn('tenant_id');
            }

            if (Schema::hasColumn('categories', 'name')) {
                $table->dropColumn('name');
            }

            if (Schema::hasColumn('categories', 'slug')) {
                $table->dropColumn('slug');
            }

            if (Schema::hasColumn('categories', 'template_prompt')) {
                $table->dropColumn('template_prompt');
            }

            if (Schema::hasColumn('categories', 'is_custom')) {
                $table->dropColumn('is_custom');
            }

            if (Schema::hasColumn('categories', 'created_by')) {
                $table->dropColumn('created_by');
            }
        });
    }
};
