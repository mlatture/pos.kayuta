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
        $tableName = 'products';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, static function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn($tableName, 'name')) {
                    $table->string('name')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'image')) {
                    $table->string('image')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'barcode')) {
                    $table->string('barcode')->unique()->nullable();
                }
                if (!Schema::hasColumn($tableName, 'price')) {
                    $table->decimal('price', 14, 2);
                }
                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->boolean('status')->default(1);
                }
                if (!Schema::hasColumn($tableName, 'organization_id')) {
                    $table->unsignedInteger('organization_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'product_vendor_id')) {
                    $table->unsignedInteger('product_vendor_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, static function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->text('description')->nullable();
                $table->string('image')->nullable();
                $table->string('barcode')->unique()->nullable();
                $table->decimal('price', 14, 2);
                $table->boolean('status')->default(1);
                $table->unsignedInteger('organization_id')->nullable();
                $table->unsignedInteger('product_vendor_id')->nullable();
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
        Schema::dropIfExists('products');
    }
};
