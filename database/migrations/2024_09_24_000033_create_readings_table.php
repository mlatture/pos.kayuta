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
        Schema::create('readings', static function (Blueprint $table) {
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
