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
    public function up()
    {
        Schema::table('scheduled_payments', function (Blueprint $table) {
            if (Schema::hasColumn('scheduled_payments', 'customer_id')) {
                try {
                    DB::statement('ALTER TABLE scheduled_payments DROP FOREIGN KEY scheduled_payments_customer_id_foreign');
                } catch (\Throwable $e) {
                    // ignore if FK doesn't exist
                }
                $table->dropColumn('customer_id');
            }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scheduled_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('scheduled_payments', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable();
            }
        });
    }
};
