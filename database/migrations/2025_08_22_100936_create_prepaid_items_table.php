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
    public function up()
    {
        Schema::create('prepaid_items', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('customer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('item_code');
            $table->string('name'); 

            $table->unsignedInteger('qty_total')->default(0);
            $table->unsignedInteger('qty_used')->default(0);

            $table->decimal('value_each', 10, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes / constraints
            $table->index(['customer_id', 'item_code']);
          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prepaid_items');
    }
};
