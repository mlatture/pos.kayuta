<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('registers')) {
            Schema::create('registers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->softDeletes();
                $table->timestamps();
            });

        
            DB::table('registers')->insertOrIgnore([
                ['name' => 'Register 1','created_at' => now(), 'updated_at' => now()],
                ['name' => 'Register 2', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registers');
    }
};
