<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBookingChannelsForApiKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_channels', function (Blueprint $table) {
            // Legacy cols from M0 (api_key, api_secret) are removed in favor of hashed keys
            if (Schema::hasColumn('booking_channels', 'api_key')) {
                $table->dropUnique(['api_key']);
                $table->dropColumn(['api_key']);
            }
            if (Schema::hasColumn('booking_channels', 'api_secret')) {
                $table->dropColumn(['api_secret']);
            }

            // Scope + key management
            $table->unsignedBigInteger('property_id')->after('id');
            $table->unsignedBigInteger('channel_id')->after('property_id');
            $table->string('api_key_hash', 64)->unique()->after('name'); // sha256 hex
            $table->enum('status', ['active','inactive'])->default('active')->after('is_active');
            $table->boolean('sandbox')->default(false)->after('status');
            $table->timestamp('last_used_at')->nullable()->after('sandbox');
            $table->boolean('auto_disabled')->default(false)->after('last_used_at');
            $table->unsignedInteger('rate_limit_per_minute')->default(100)->after('auto_disabled');
            $table->unsignedInteger('rate_burst_per_minute')->default(300)->after('rate_limit_per_minute');

            $table->index(['property_id', 'channel_id']);
            $table->index('last_used_at');
            $table->index(['status','auto_disabled']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_channels', function (Blueprint $table) {
            $table->dropIndex(['property_id', 'channel_id']);
            $table->dropIndex(['last_used_at']);
            $table->dropIndex(['status','auto_disabled']);
            $table->dropColumn([
                'property_id','channel_id','api_key_hash','status','sandbox','last_used_at',
                'auto_disabled','rate_limit_per_minute','rate_burst_per_minute'
            ]);
            // (Optional) re-add legacy cols if needed
            // $table->string('api_key')->unique();
            // $table->string('api_secret');
        });
    }
}
