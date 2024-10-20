<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableName = 'site_hookups';

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('sitehookup')->nullable();
                $table->integer('orderby')->default(0);
                $table->timestamps();
            });
        } else {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'sitehookup')) {
                    $table->string('sitehookup')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'orderby')) {
                    $table->integer('orderby')->default(0);
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
     */
    public function down(): void
    {
        Schema::dropIfExists('site_hookups');
    }
};
