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
        $tableName = 'failed_jobs';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, static function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn($tableName, 'connection')) {
                    $table->text('connection');
                }
                if (!Schema::hasColumn($tableName, 'queue')) {
                    $table->text('queue');
                }
                if (!Schema::hasColumn($tableName, 'payload')) {
                    $table->longText('payload');
                }
                if (!Schema::hasColumn($tableName, 'exception')) {
                    $table->longText('exception');
                }
                if (!Schema::hasColumn($tableName, 'failed_at')) {
                    $table->timestamp('failed_at')->useCurrent();
                }
            });
        } else {
            Schema::create($tableName, static function (Blueprint $table) {
                $table->id();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
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
        Schema::dropIfExists('failed_jobs');
    }
};
