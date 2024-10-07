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
    public function up()
    {
        $tableName = 'products';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                // Change 'quantity' column if it doesn't exist
                if (!Schema::hasColumn($tableName, 'quantity')) {
                    $table->string('quantity')->nullable()->change();
                }

                // Add 'cost' column if it doesn't exist
                if (!Schema::hasColumn($tableName, 'cost')) {
                    $table->float('cost')->nullable();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id(); // Assuming you have an id column
                $table->string('quantity')->nullable();
                $table->float('cost')->nullable();
                $table->timestamps(); // If you need timestamps
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Revert 'quantity' to integer
            if (Schema::hasColumn('products', 'quantity')) {
                $table->integer('quantity')->change();
            }

            // Drop 'cost' column if it exists
            if (Schema::hasColumn('products', 'cost')) {
                $table->dropColumn('cost');
            }

            if (Schema::hasColumn('products', 'organization_id')) {
                $table->dropColumn('organization_id');
            }
        });
    }
};
