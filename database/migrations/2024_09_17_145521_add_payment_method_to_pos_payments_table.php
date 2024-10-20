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
        $tableName = 'pos_payments';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'payment_method')) {
                    $table->string('payment_method')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'payment_acc_number')) {
                    $table->string('payment_acc_number')->nullable();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->integer('organization_id')->nullable();
                $table->double('amount')->default(0);
                $table->bigInteger('order_id')->nullable();
                $table->bigInteger('admin_id')->nullable();
                $table->string('payment_method')->nullable();
                $table->string('payment_acc_number')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('pos_payments', static function (Blueprint $table) {
            $table->dropColumn('payment_method');
            $table->dropColumn('payment_acc_number');

        });
    }
};
