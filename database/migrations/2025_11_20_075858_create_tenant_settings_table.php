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
       Schema::create('tenant_settings', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('tenant_id')->unique();
    $table->boolean('autopilot_enabled')->default(false);
    $table->json('preferences')->nullable(); // tones, audiences defaults etc
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
        Schema::dropIfExists('tenant_settings');
    }
};
