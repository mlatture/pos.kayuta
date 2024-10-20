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
        $tableName = 'sessions';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->string('id')->primary();
                }
                if (!Schema::hasColumn($tableName, 'user_id')) {
                    $table->foreignId('user_id')->nullable()->index();
                }
                if (!Schema::hasColumn($tableName, 'ip_address')) {
                    $table->string('ip_address', 45)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'user_agent')) {
                    $table->text('user_agent')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'payload')) {
                    $table->longText('payload')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'last_activity')) {
                    $table->integer('last_activity')->index();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload')->nullable();
                $table->integer('last_activity')->index();
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
        Schema::dropIfExists('sessions');
    }
};
