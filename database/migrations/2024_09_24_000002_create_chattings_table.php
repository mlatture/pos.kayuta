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
        $tableName = 'chattings';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'message')) {
                    $table->text('message')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'sent_by_customer')) {
                    $table->boolean('sent_by_customer')->default(0);
                }
                if (!Schema::hasColumn($tableName, 'sent_by_seller')) {
                    $table->boolean('sent_by_seller')->default(0);
                }
                if (!Schema::hasColumn($tableName, 'sent_by_admin')) {
                    $table->boolean('sent_by_admin')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'sent_by_delivery_man')) {
                    $table->boolean('sent_by_delivery_man')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'seen_by_customer')) {
                    $table->boolean('seen_by_customer')->default(1);
                }
                if (!Schema::hasColumn($tableName, 'seen_by_seller')) {
                    $table->boolean('seen_by_seller')->default(1);
                }
                if (!Schema::hasColumn($tableName, 'seen_by_admin')) {
                    $table->boolean('seen_by_admin')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'seen_by_delivery_man')) {
                    $table->boolean('seen_by_delivery_man')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->boolean('status')->default(1);
                }
                if (!Schema::hasColumn($tableName, 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'seller_id')) {
                    $table->unsignedBigInteger('seller_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'admin_id')) {
                    $table->unsignedBigInteger('admin_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'delivery_man_id')) {
                    $table->unsignedBigInteger('delivery_man_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'shop_id')) {
                    $table->unsignedBigInteger('shop_id')->nullable();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->text('message');
                $table->boolean('sent_by_customer')->default(0);
                $table->boolean('sent_by_seller')->default(0);
                $table->boolean('sent_by_admin')->nullable();
                $table->boolean('sent_by_delivery_man')->nullable();
                $table->boolean('seen_by_customer')->default(1);
                $table->boolean('seen_by_seller')->default(1);
                $table->boolean('seen_by_admin')->nullable();
                $table->boolean('seen_by_delivery_man')->nullable();
                $table->boolean('status')->default(1);
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('seller_id')->nullable();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->unsignedBigInteger('delivery_man_id')->nullable();
                $table->unsignedBigInteger('shop_id')->nullable();
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
        Schema::dropIfExists('chattings');
    }
};
