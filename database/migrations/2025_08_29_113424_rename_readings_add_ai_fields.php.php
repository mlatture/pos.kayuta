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
        Schema::rename('readings', 'electric_readings');

        Schema::table('electric_readings', function (Blueprint $table) {
            $table->string('meter_style')->nullable();
            $table->string('manufacturer')->nullable();

            $table->string('ai_meter_number')->nullable();
            $table->decimal('ai_meter_reading', 10, 3)->nullable();

            $table->boolean('ai_success')->default(true); // hidden from UI
            $table->boolean('ai_fixed')->default(false);

            $table->string('prompt_version')->nullable();
            $table->string('model_version')->nullable();
            $table->integer('ai_latency_ms')->nullable();

            // optional quality/telemetry you may want soon:
            // $table->float('image_quality_score')->nullable();
            // $table->float('blur_score')->nullable();
            // $table->float('glare_score')->nullable();
            // $table->float('exposure_score')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
