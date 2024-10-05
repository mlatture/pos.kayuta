<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
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
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn($tableName, 'organization_id')) {
                    $table->integer('organization_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'amount')) {
                    $table->double('amount')->default(0);
                }
                if (!Schema::hasColumn($tableName, 'order_id')) {
                    $table->bigInteger('order_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'admin_id')) {
                    $table->bigInteger('admin_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->integer('organization_id')->nullable();
                $table->double('amount')->default(0);
                $table->bigInteger('order_id')->nullable();
                $table->bigInteger('admin_id')->nullable();
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
        Schema::dropIfExists('pos_payments');
    }
};
