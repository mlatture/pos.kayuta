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
        Schema::create('gift_cards', static function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('user_email')->nullable();
            $table->string('barcode')->nullable();
            $table->string('discount_type')->comment('percentage, fixed_amount')->nullable();
            $table->double('discount')->default(0);
            $table->date('start_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->double('min_purchase')->default(0);
            $table->double('max_discount')->default(0);
            $table->integer('limit')->nullable();
            $table->boolean('status')->default(0);
            $table->integer('organization_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
    }
};
