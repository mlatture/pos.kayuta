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
        try {
            Schema::table('reservations', function (Blueprint $table) {
                $table->index('cartid');
                $table->index('fname');
                $table->index('lname');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'phone')) {
                     $table->index('phone');
                }
                $table->index('f_name');
                $table->index('l_name');
            });
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['cartid']);
            $table->dropIndex(['fname']);
            $table->dropIndex(['lname']);
        });

        Schema::table('users', function (Blueprint $table) {
             if (Schema::hasColumn('users', 'phone')) {
                 $table->dropIndex(['phone']);
             }
             $table->dropIndex(['f_name']);
             $table->dropIndex(['l_name']);
        });
    }
};
