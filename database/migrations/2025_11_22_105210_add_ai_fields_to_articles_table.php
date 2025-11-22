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
        Schema::table('articles', function (Blueprint $table) {
            if (! Schema::hasColumn('articles', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')
                    ->default(1)
                    ->after('id')
                    ->index();
            }

            if (! Schema::hasColumn('articles', 'idea_id')) {
                $table->unsignedBigInteger('idea_id')
                    ->nullable()
                    ->after('tenant_id')
                    ->index();
            }

            if (! Schema::hasColumn('articles', 'views')) {
                $table->unsignedBigInteger('views')
                    ->default(0)
                    ->after('status');
            }

            if (! Schema::hasColumn('articles', 'referrers')) {
                $table->json('referrers')
                    ->nullable()
                    ->after('views');
            }
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (Schema::hasColumn('articles', 'tenant_id')) {
                $table->dropColumn('tenant_id');
            }
            if (Schema::hasColumn('articles', 'idea_id')) {
                $table->dropColumn('idea_id');
            }
            if (Schema::hasColumn('articles', 'views')) {
                $table->dropColumn('views');
            }
            if (Schema::hasColumn('articles', 'referrers')) {
                $table->dropColumn('referrers');
            }
        });
    }
};
