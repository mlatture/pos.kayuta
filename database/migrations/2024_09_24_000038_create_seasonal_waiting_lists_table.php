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
        Schema::create('seasonal_waiting_lists', static function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 255)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 255)->nullable();
            $table->string('state', 255)->nullable();
            $table->string('zip', 255)->nullable();
            $table->string('no_of_adults', 255)->nullable();
            $table->string('no_of_children_below_thirteen', 255)->nullable();
            $table->string('no_of_children_above_twelve', 255)->nullable();
            $table->string('camper_type', 255)->nullable();
            $table->string('camper_year', 255)->nullable();
            $table->string('length_of_camper', 255)->nullable();
            $table->boolean('camped_before')->default(0);
            $table->string('last_visit', 255)->nullable();
            $table->string('hear_about_us', 255)->nullable();
            $table->string('reference', 255)->nullable();
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
        Schema::dropIfExists('seasonal_waiting_lists');
    }
};
