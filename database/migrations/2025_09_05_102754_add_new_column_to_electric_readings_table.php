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
        Schema::table('electric_readings', function (Blueprint $table) {
            $table->string('ai_confidence', 10)->nullable()->after('ai_fixed')->comment('AI confidence level: high, medium, low');

            $table->text('ai_notes')->nullable()->after('ai_confidence')->comment('AI notes about reading quality or issues');

            $table->integer('ai_attempts')->default(1)->after('ai_notes')->comment('Number of AI attempts needed');

            $table->index('ai_confidence');
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
            $table->dropIndex(['ai_confidence']);
            $table->dropColumn(['ai_confidence', 'ai_notes', 'ai_attempts']);
        });
    }
};
