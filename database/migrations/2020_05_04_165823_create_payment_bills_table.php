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
        $tableName = 'payment_bills';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn($tableName, 'method')) {
                    $table->string('method', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'site')) {
                    $table->string('site', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'payment')) {
                    $table->string('payment', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'customer_id')) {
                    $table->unsignedBigInteger('customer_id');
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('method', 255)->nullable();
                $table->string('site', 255)->nullable();
                $table->string('payment', 255)->nullable();
                $table->unsignedBigInteger('customer_id');
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
        Schema::dropIfExists('payment_bills');
    }
};
