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
        $tableName = 'gift_cards';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn($tableName, 'title')) {
                    $table->string('title')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'user_email')) {
                    $table->string('user_email')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'barcode')) {
                    $table->string('barcode')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'discount_type')) {
                    $table->string('discount_type')->comment('percentage, fixed_amount')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'discount')) {
                    $table->double('discount')->default(0);
                }
                if (!Schema::hasColumn($tableName, 'start_date')) {
                    $table->date('start_date')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'expire_date')) {
                    $table->date('expire_date')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'min_purchase')) {
                    $table->double('min_purchase')->default(0);
                }
                if (!Schema::hasColumn($tableName, 'max_discount')) {
                    $table->double('max_discount')->default(0);
                }
                if (!Schema::hasColumn($tableName, 'limit')) {
                    $table->integer('limit')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->boolean('status')->default(0);
                }
                if (!Schema::hasColumn($tableName, 'organization_id')) {
                    $table->integer('organization_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('user_email')->nullable();
                $table->string('barcode')->nullable();
                $table->string('discount_type')->comment('percentage, fixed_amount')->nullable();
                $table->double('discount')->default(0);
                $table->date('start_date')->nullable();
                $table->date('expire_date')->nullable();
                $table->double('min_purchase')->default(0);
                $table->double('max_discount')->default(0);
                $table->integer('limit')->nullable();
                $table->boolean('status')->default(0);
                $table->integer('organization_id')->nullable();
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
        Schema::dropIfExists('gift_cards');
    }
};
