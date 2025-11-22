<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {

            // Multi-tenant support
            if (!Schema::hasColumn('pages', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')
                    ->default(1)
                    ->after('id')
                    ->index();
            }

            // Link back to content_ideas
            if (!Schema::hasColumn('pages', 'idea_id')) {
                $table->unsignedBigInteger('idea_id')
                    ->nullable()
                    ->after('tenant_id')
                    ->index();
            }

            // Basic tracking
            if (!Schema::hasColumn('pages', 'views')) {
                $table->unsignedBigInteger('views')
                    ->default(0)
                    ->after('status');
            }

            if (!Schema::hasColumn('pages', 'referrers')) {
                $table->json('referrers')
                    ->nullable()
                    ->after('views');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {

            if (Schema::hasColumn('pages', 'tenant_id')) {
                $table->dropColumn('tenant_id');
            }

            if (Schema::hasColumn('pages', 'idea_id')) {
                $table->dropColumn('idea_id');
            }

            if (Schema::hasColumn('pages', 'views')) {
                $table->dropColumn('views');
            }

            if (Schema::hasColumn('pages', 'referrers')) {
                $table->dropColumn('referrers');
            }
        });
    }
};
