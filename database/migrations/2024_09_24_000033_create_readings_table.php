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
        $tableName = 'readings';

        if (Schema::hasTable($tableName)) {
            // If the table exists, modify it
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'kwhNo')) {
                    $table->string('kwhNo')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'image')) {
                    $table->text('image')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'date')) {
                    $table->date('date')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'siteno')) {
                    $table->string('siteno')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->string('status')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'bill')) {
                    $table->string('bill')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'customer_id')) {
                    $table->unsignedBigInteger('customer_id');
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
                $table->string('kwhNo')->nullable();
                $table->text('image')->nullable();
                $table->date('date')->nullable();
                $table->string('siteno')->nullable();
                $table->string('status')->nullable();
                $table->string('bill')->nullable();
                $table->unsignedBigInteger('customer_id');
                $table->timestamps();

                // Foreign Key
                //$table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
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
        Schema::dropIfExists('readings');
    }
};
