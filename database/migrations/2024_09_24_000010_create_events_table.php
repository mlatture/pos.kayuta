<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('events', static function (Blueprint $table) {
            $table->id();
            $table->string('eventname', 50);
            $table->date('eventstart');
            $table->date('eventend');
            $table->integer('minimumstay')->nullable();
            $table->longText('bookingmessage')->nullable();
            $table->longText('description')->nullable();
            $table->string('embeddedvideo', 50)->nullable();
            $table->decimal('extracharge', 10, 0)->nullable();
            $table->decimal('extranightlycharge', 10, 0)->nullable();
            $table->string('poster', 50)->nullable();
            $table->longText('previewdescription')->nullable();
            $table->string('eventcode', 50)->nullable();
            $table->string('headergraphic', 50)->nullable();
            $table->timestamp('lastmodified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('events');
    }
};
