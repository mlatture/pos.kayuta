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
        $tableName = 'admin_cart';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn($tableName, 'admin_id')) {
                    //$table->foreignId('admin_id')->nullable();
                    $table->unsignedBigInteger('admin_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'product_id')) {
                    //$table->foreignId('product_id')->nullable();
                    $table->unsignedBigInteger('product_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'quantity')) {
                    $table->unsignedInteger('quantity')->nullable();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->unsignedBigInteger('product_id')->nullable();
                //$table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('cascade');
                //$table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
                $table->unsignedInteger('quantity')->nullable();
            });
        }
//        Schema::create('admin_cart', static function (Blueprint $table) {
//            $table->id();
//            $table->foreignId('admin_id')->nullable();
//            $table->foreignId('product_id')->nullable();
//            $table->unsignedInteger('quantity')->nullable();
//            $table->unsignedBigInteger('admin_id')->nullable();
//            $table->unsignedBigInteger('product_id')->nullable();
//
//            //$table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
//            //$table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_cart');
    }
};
