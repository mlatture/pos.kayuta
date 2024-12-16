<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToCampingSeasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('camping_seasons', function (Blueprint $table) {
            $table->enum('type', ['pool', 'park'])->default('park'); // Add enum field with default
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('camping_seasons', function (Blueprint $table) {
            $table->dropColumn('type');

        });
    }
}
