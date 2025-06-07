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
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('metatitle')->nullable();
            $table->text('metadescription')->nullable();
            $table->string('canonicalurl')->nullable();
            $table->string('opengraphtitle')->nullable();
            $table->text('opengraphdescription')->nullable();
            $table->string('opengraphimage')->nullable();
        });
    
        Schema::table('articles', function (Blueprint $table) {
            $table->string('metatitle')->nullable();
            $table->text('metadescription')->nullable();
            $table->string('canonicalurl')->nullable();
            $table->string('opengraphtitle')->nullable();
            $table->text('opengraphdescription')->nullable();
            $table->string('opengraphimage')->nullable();
        });
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn([
                'metatitle', 'metadescription', 'canonicalurl',
                'opengraphtitle', 'opengraphdescription', 'opengraphimage'
            ]);
        });
    
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'metatitle', 'metadescription', 'canonicalurl',
                'opengraphtitle', 'opengraphdescription', 'opengraphimage'
            ]);
        });
    
    }
};
