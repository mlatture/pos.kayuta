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
        Schema::table('seasonal_rates', function (Blueprint $table) {
            $table->dropForeign(['template_id']);

            // Make the column nullable
            $table->foreignId('template_id')->nullable()->change();

            // Re-add the foreign key with ON DELETE SET NULL
            $table->foreign('template_id')->references('id')->on('document_templates')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seasonal_rates', function (Blueprint $table) {
            //
        });
    }
};
