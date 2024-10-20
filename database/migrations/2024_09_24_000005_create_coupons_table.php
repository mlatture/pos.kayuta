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
        $tableName = 'coupons';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'added_by')) {
                    $table->string('added_by', 191)->default('admin');
                }
                if (!Schema::hasColumn($tableName, 'coupon_type')) {
                    $table->string('coupon_type', 50)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'coupon_bearer')) {
                    $table->string('coupon_bearer', 191)->default('inhouse');
                }
                if (!Schema::hasColumn($tableName, 'title')) {
                    $table->string('title', 100)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'code')) {
                    $table->string('code', 15)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'start_date')) {
                    $table->date('start_date')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'expire_date')) {
                    $table->date('expire_date')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'min_purchase')) {
                    $table->decimal('min_purchase', 8, 2)->default(0.00);
                }
                if (!Schema::hasColumn($tableName, 'max_discount')) {
                    $table->decimal('max_discount', 8, 2)->default(0.00);
                }
                if (!Schema::hasColumn($tableName, 'discount')) {
                    $table->decimal('discount', 8, 2)->default(0.00);
                }
                if (!Schema::hasColumn($tableName, 'discount_type')) {
                    $table->string('discount_type', 15)->default('percentage');
                }
                if (!Schema::hasColumn($tableName, 'limit')) {
                    $table->integer('limit')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->boolean('status')->default(1);
                }
                if (!Schema::hasColumn($tableName, 'seller_id')) {
                    $table->unsignedBigInteger('seller_id')->nullable()->comment('NULL=in-house, 0=all seller');
                }
                if (!Schema::hasColumn($tableName, 'customer_id')) {
                    $table->unsignedBigInteger('customer_id')->nullable()->comment('0 = all customer');
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('added_by', 191)->default('admin');
                $table->string('coupon_type', 50)->nullable();
                $table->string('coupon_bearer', 191)->default('inhouse');
                $table->string('title', 100)->nullable();
                $table->string('code', 15)->nullable();
                $table->date('start_date')->nullable();
                $table->date('expire_date')->nullable();
                $table->decimal('min_purchase', 8, 2)->default(0.00);
                $table->decimal('max_discount', 8, 2)->default(0.00);
                $table->decimal('discount', 8, 2)->default(0.00);
                $table->string('discount_type', 15)->default('percentage');
                $table->integer('limit')->nullable();
                $table->boolean('status')->default(1);
                $table->unsignedBigInteger('seller_id')->nullable()->comment('NULL=in-house, 0=all seller');
                $table->unsignedBigInteger('customer_id')->nullable()->comment('0 = all customer');
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
        Schema::dropIfExists('coupons');
    }
};
