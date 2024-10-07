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
        $tableName = 'order_items';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn($tableName, 'price')) {
                    $table->decimal('price', 14, 4)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'quantity')) {
                    $table->integer('quantity')->default(1);
                }
                if (!Schema::hasColumn($tableName, 'organization_id')) {
                    $table->unsignedInteger('organization_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'order_id')) {
                    //$table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                    $table->unsignedBigInteger('order_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'product_id')) {
                    //$table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                    $table->unsignedBigInteger('product_id')->nullable();
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
                $table->decimal('price', 14, 4)->nullable();
                $table->integer('quantity')->default(1);
                $table->unsignedInteger('organization_id')->nullable();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->unsignedBigInteger('product_id')->nullable();
                //$table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                //$table->foreignId('product_id')->constrained('products')->onDelete('cascade');
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
        Schema::dropIfExists('order_items');
    }
};
