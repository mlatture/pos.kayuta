<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableName = 'sites';

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('organization_id')->nullable();
                $table->string('siteid')->nullable();
                $table->string('sitename')->nullable();
                $table->string('siteclass')->nullable();
                $table->string('hookup')->nullable();
                $table->boolean('availableonline')->default(1)->nullable();
                $table->boolean('available')->default(1)->nullable();
                $table->boolean('seasonal')->default(0)->nullable();
                $table->integer('maxlength')->nullable();
                $table->integer('minlength')->nullable();
                $table->json('rigtypes')->nullable();
                $table->string('class')->nullable();
                $table->string('coordinates')->nullable();
                $table->string('attributes')->nullable();
                $table->json('amenities')->nullable();
                $table->text('description')->nullable();
                $table->string('ratetier')->nullable();
                $table->string('tax')->nullable();
                $table->integer('minimumstay')->default(1)->nullable();
                $table->string('sitesection')->nullable();
                $table->string('youtube')->nullable();
                $table->string('vt_tour')->nullable();
                $table->string('embeddedvideo')->nullable();
                $table->string('lastmeterreading')->nullable();
                $table->integer('orderby')->default(0)->nullable();
                $table->string('lastmodified')->nullable();
                $table->json('images')->nullable();
                $table->timestamps();
                $table->string('photo_360_url')->nullable();
            });
        } else {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'organization_id')) {
                    $table->unsignedBigInteger('organization_id')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'siteid')) {
                    $table->string('siteid')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'sitename')) {
                    $table->string('sitename')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'siteclass')) {
                    $table->string('siteclass')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'hookup')) {
                    $table->string('hookup')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'availableonline')) {
                    $table->boolean('availableonline')->default(1);
                }

                if (!Schema::hasColumn($tableName, 'available')) {
                    $table->boolean('available')->default(1);
                }

                if (!Schema::hasColumn($tableName, 'seasonal')) {
                    $table->boolean('seasonal')->default(0);
                }

                if (!Schema::hasColumn($tableName, 'maxlength')) {
                    $table->integer('maxlength')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'minlength')) {
                    $table->integer('minlength')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'rigtypes')) {
                    $table->json('rigtypes')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'class')) {
                    $table->string('class')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'coordinates')) {
                    $table->string('coordinates')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'attributes')) {
                    $table->string('attributes')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'amenities')) {
                    $table->json('amenities')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'description')) {
                    $table->text('description')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'ratetier')) {
                    $table->string('ratetier')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'tax')) {
                    $table->string('tax')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'minimumstay')) {
                    $table->integer('minimumstay')->default(1);
                }

                if (!Schema::hasColumn($tableName, 'sitesection')) {
                    $table->string('sitesection')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'youtube')) {
                    $table->string('youtube')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'vt_tour')) {
                    $table->string('vt_tour')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'embeddedvideo')) {
                    $table->string('embeddedvideo')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'lastmeterreading')) {
                    $table->dateTime('lastmeterreading')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'orderby')) {
                    $table->integer('orderby')->default(0);
                }

                if (!Schema::hasColumn($tableName, 'lastmodified')) {
                    $table->dateTime('lastmodified')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'images')) {
                    $table->json('images')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'photo_360_url')) {
                    $table->string('photo_360_url')->nullable();
                }

                // Check for timestamps
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
                }

                if (!Schema::hasColumn($tableName, 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
