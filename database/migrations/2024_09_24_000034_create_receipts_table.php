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
        $tableName = 'receipts';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'cartid')) {
                    $table->string('cartid', 30)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'createdate')) {
                    $table->dateTime('createdate')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
                }

                if (!Schema::hasColumn($tableName, 'lastmodified')) {
                    $table->dateTime('lastmodified')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
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
                $table->string('cartid', 30)->nullable();
                $table->dateTime('createdate')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
                $table->dateTime('lastmodified')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
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
        Schema::dropIfExists('receipts');
    }
};
