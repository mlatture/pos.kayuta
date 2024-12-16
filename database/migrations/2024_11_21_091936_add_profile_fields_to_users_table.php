<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add 'state' column if it doesn't already exist
            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state')->default('NY');
            }

            // Add 'text_on_phone' column if it doesn't already exist
            if (!Schema::hasColumn('users', 'text_on_phone')) {
                $table->enum('text_on_phone', ['yes', 'no'])->default('yes');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop 'state' column if it exists
            if (Schema::hasColumn('users', 'state')) {
                $table->dropColumn('state');
            }

            // Drop 'text_on_phone' column if it exists
            if (Schema::hasColumn('users', 'text_on_phone')) {
                $table->dropColumn('text_on_phone');
            }
        });
    }
}
