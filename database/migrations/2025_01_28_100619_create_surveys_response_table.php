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
        Schema::create('surveys_response', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('siteId');
            $table->integer('survey_id');
            $table->json('questions');
            $table->json('answers');
            $table->string('token')->nullable();
            $table->unique('token', 'unique_tokens');
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
        Schema::dropIfExists('surveys_response');
    }
};
