<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddListingFieldsToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('golf_listing_show')->default(false);
            $table->boolean('boat_listing_show')->default(false);
            $table->boolean('pool_listing_show')->default(false);
            $table->boolean('product_listing_show')->default(false);
            $table->unsignedBigInteger('organization_id')->nullable(); // Foreign key for the organization

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['golf_listing_show', 'boat_listing_show', 'pool_listing_show', 'product_listing_show','organization_id']);
        });
    }
}
