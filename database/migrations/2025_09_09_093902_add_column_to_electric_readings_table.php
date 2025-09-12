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
        Schema::table('electric_readings', function (Blueprint $table) {
            if (!Schema::hasColumn('electric_readings', 'training_opt_in')) {
                $table->boolean('training_opt_in')->default(false)->index();
            }

            $table->index(['meter_number', 'date']);
            $table->index(['ai_success', 'ai_fixed']);
            $table->index(['manufacturer', 'meter_style']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('electric_readings', function (Blueprint $table) {
            //
        });
    }
};
