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
        Schema::create('seasonal_customer_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->enum('discount_type', ['percentage', 'dollar']);
            $table->decimal('discount_value', 8, 2);
            $table->text('description');
            $table->boolean('is_active')->default(true);
            $table->integer('season_year')->index();
            $table->foreignId('created_by')->nullable()->constrained('admins');
            $table->timestamps();

            $table->index(['customer_id', 'season_year']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seasonal_customer_discounts');
    }
};
