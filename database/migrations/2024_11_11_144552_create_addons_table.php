<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the table already exists before attempting to create it
        if (!Schema::hasTable('addons')) {
            Schema::create('addons', function (Blueprint $table) {
                $table->id(); // Primary key
                $table->string('addon_name'); // Name of the add-on (e.g., "4-seater golf cart")
                $table->float('price'); // Price for the add-on
                $table->enum('addon_type', ['golf cart', 'pool cabana']); // Enum for add-on type
                $table->integer('capacity'); // Number of seats or capacity

                // Uncomment these lines if you want to add inventory and availability
                // $table->integer('inventory_count'); // Number of units available per day
                // $table->date('start_date'); // Start date for availability
                // $table->date('end_date'); // End date for availability
                
                $table->timestamps(); // Created at and updated at columns
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
        Schema::dropIfExists('addons');
    }
}
