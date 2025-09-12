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
         if (!Schema::hasTable('electric_bills')) {
            Schema::create('electric_bills', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('reading_id')->index();
                $table->string('meter_number', 32)->index();
                $table->unsignedBigInteger('customer_id')->nullable()->index();
                $table->date('start_date'); $table->date('end_date');
                $table->decimal('usage_kwh', 12, 3)->default(0);
                $table->decimal('rate', 10, 4)->default(0);
                $table->decimal('total', 12, 2)->default(0);
                $table->decimal('threshold_used', 12, 2)->default(0);
                $table->boolean('warning_overridden')->default(false);
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
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
        Schema::dropIfExists('electric_bills');
    }
};
