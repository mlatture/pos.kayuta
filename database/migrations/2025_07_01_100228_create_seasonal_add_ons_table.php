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
        Schema::create('seasonal_add_ons', function (Blueprint $table) {
            $table->id();
            $table->string('seasonal_add_on_name');
            $table->decimal('seasonal_add_on_price', 8, 2)->default(0);
            $table->integer('max_allowed')->default(1);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seasonal_add_ons');
    }
};
