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
        $tableName = 'orders';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn($tableName, 'organization_id')) {
                    $table->unsignedInteger('organization_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'admin_id')) {
                    $table->unsignedBigInteger('admin_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'customer_id')) {
                    $table->unsignedBigInteger('customer_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }
                if (!Schema::hasColumn($tableName, 'updated_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('organization_id')->nullable();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->timestamps();

                // Indexes
                $table->index('customer_id');
                $table->index('user_id');

                // Foreign keys
                //$table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
                //$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('orders');
    }
};
