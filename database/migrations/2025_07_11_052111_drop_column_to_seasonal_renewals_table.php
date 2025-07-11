<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        try {
            Schema::table('seasonal_renewals', function (Blueprint $table) {
                // Drop FK for customer_id only
                if (Schema::hasColumn('seasonal_renewals', 'customer_id')) {
                    try {
                        DB::statement('ALTER TABLE seasonal_renewals DROP FOREIGN KEY seasonal_renewals_customer_id_foreign');
                    } catch (\Throwable $e) {
                        // ignore if FK doesn't exist
                    }
                    $table->dropColumn('customer_id');
                }

                // Drop plain column payment_plan_id
                if (Schema::hasColumn('seasonal_renewals', 'payment_plan_id')) {
                    $table->dropColumn('payment_plan_id');
                }

                foreach (['selected_payment_method', 'offered_rate', 'renewed', 'response_date', 'notes'] as $column) {
                    if (Schema::hasColumn('seasonal_renewals', $column)) {
                        $table->dropColumn($column);
                    }
                }

                if (!Schema::hasColumn('seasonal_renewals', 'selected_card')) {
                    $table->string('selected_card')->nullable()->comment('With Masked Info');
                }
            });
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function down()
    {
        try {
            Schema::table('seasonal_renewals', function (Blueprint $table) {
                if (!Schema::hasColumn('seasonal_renewals', 'customer_id')) {
                    $table->unsignedBigInteger('customer_id')->nullable();
                    // optionally: $table->foreign('customer_id')->references('id')->on('users');
                }

                if (!Schema::hasColumn('seasonal_renewals', 'payment_plan_id')) {
                    $table->unsignedBigInteger('payment_plan_id')->nullable();
                }

                if (!Schema::hasColumn('seasonal_renewals', 'selected_payment_method')) {
                    $table->string('selected_payment_method')->nullable();
                }

                if (!Schema::hasColumn('seasonal_renewals', 'offered_rate')) {
                    $table->decimal('offered_rate', 10, 2)->nullable();
                }

                if (!Schema::hasColumn('seasonal_renewals', 'renewed')) {
                    $table->boolean('renewed')->default(false);
                }

                if (!Schema::hasColumn('seasonal_renewals', 'response_date')) {
                    $table->dateTime('response_date')->nullable();
                }

                if (!Schema::hasColumn('seasonal_renewals', 'note')) {
                    $table->text('note')->nullable();
                }

                if (Schema::hasColumn('seasonal_renewals', 'selected_card')) {
                    $table->dropColumn('selected_card');
                }
            });
        } catch (\Throwable $e) {
            throw $e;
        }
    }
};
