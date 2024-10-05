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
        $tableName = 'reviews';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'comment')) {
                    $table->mediumText('comment')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'attachment')) {
                    $table->string('attachment', 191)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'rating')) {
                    $table->integer('rating')->default(0);
                }

                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->integer('status')->default(1);
                }

                if (!Schema::hasColumn($tableName, 'is_saved')) {
                    $table->boolean('is_saved')->default(0);
                }

                if (!Schema::hasColumn($tableName, 'type')) {
                    $table->string('type', 191)->default('product');
                }

                if (!Schema::hasColumn($tableName, 'product_id')) {
                    $table->unsignedBigInteger('product_id')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'customer_id')) {
                    $table->unsignedBigInteger('customer_id')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'delivery_man_id')) {
                    $table->unsignedBigInteger('delivery_man_id')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'order_id')) {
                    $table->unsignedBigInteger('order_id')->nullable();
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
                $table->mediumText('comment')->nullable();
                $table->string('attachment', 191)->nullable();
                $table->integer('rating')->default(0);
                $table->integer('status')->default(1);
                $table->boolean('is_saved')->default(0);
                $table->string('type', 191)->default('product');
                $table->unsignedBigInteger('product_id')->nullable();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->unsignedBigInteger('delivery_man_id')->nullable();
                $table->unsignedBigInteger('order_id')->nullable();
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
        Schema::dropIfExists('reviews');
    }
};
