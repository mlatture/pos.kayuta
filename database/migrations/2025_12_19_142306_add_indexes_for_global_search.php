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
        Schema::table('reservations', function (Blueprint $table) {
            $table->index('cartid');
            $table->index('fname');
            $table->index('lname');
        });

        Schema::table('users', function (Blueprint $table) {
            // Email usually indexed being unique, but for partial we rely on prefix index often standard.
            // Phone might not be present in all setups, assuming it exists based on requirements.
            if (!Schema::hasColumn('users', 'phone')) {
                // If phone doesn't exist, we skip or add it. Assuming it exists.
            } else {
                 $table->index('phone');
            }
            $table->index('f_name');
            $table->index('l_name');
        });
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
