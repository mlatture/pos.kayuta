<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $tableName = 'payments';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn($tableName, 'amount')) {
                    $table->decimal('amount', 8, 4)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'organization_id')) {
                    $table->integer('organization_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'cartid')) {
                    $table->string('cartid', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'receipt')) {
                    $table->integer('receipt')->default(0);
                }
                if (!Schema::hasColumn($tableName, 'method')) {
                    $table->string('method', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'customernumber')) {
                    $table->string('customernumber', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'email')) {
                    $table->string('email', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'payment')) {
                    $table->float('payment')->default(0);
                }
                if (!Schema::hasColumn($tableName, 'order_id')) {
                    //$table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                    $table->unsignedBigInteger('order_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'user_id')) {
                    //$table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                    $table->unsignedBigInteger('users_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'createdate')) {
                    $table->dateTime('createdate')->default(DB::raw('CURRENT_TIMESTAMP'));
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->decimal('amount', 8, 4)->nullable();
                $table->integer('organization_id')->nullable();
                $table->string('cartid', 255)->nullable();
                $table->integer('receipt')->default(0);
                $table->string('method', 255)->nullable();
                $table->string('customernumber', 255)->nullable();
                $table->string('email', 255)->nullable();
                $table->float('payment')->default(0);
                $table->unsignedBigInteger('order_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                //$table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                //$table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->dateTime('createdate')->default(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('payments');
    }
};
