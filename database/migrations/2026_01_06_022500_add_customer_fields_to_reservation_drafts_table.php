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
        Schema::table('reservation_drafts', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('draft_id');
            $table->json('guest_data')->nullable()->after('cart_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_drafts', function (Blueprint $table) {
            $table->dropColumn(['customer_id', 'guest_data']);
        });
    }
};
