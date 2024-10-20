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
        $tableName = 'password_resets';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, static function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'email')) {
                    $table->string('email')->index();
                }
                if (!Schema::hasColumn($tableName, 'token')) {
                    $table->string('token');
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
            });
        } else {
            Schema::create($tableName, static function (Blueprint $table) {
                $table->string('email')->index();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
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
        Schema::dropIfExists('password_resets');
    }
};
