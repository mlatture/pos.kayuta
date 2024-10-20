<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $tableName = 'events';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'eventname')) {
                    $table->string('eventname', 50)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'eventstart')) {
                    $table->date('eventstart')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'eventend')) {
                    $table->date('eventend')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'minimumstay')) {
                    $table->integer('minimumstay')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'bookingmessage')) {
                    $table->longText('bookingmessage')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'description')) {
                    $table->longText('description')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'embeddedvideo')) {
                    $table->string('embeddedvideo', 50)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'extracharge')) {
                    $table->decimal('extracharge', 10, 0)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'extranightlycharge')) {
                    $table->decimal('extranightlycharge', 10, 0)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'poster')) {
                    $table->string('poster', 50)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'previewdescription')) {
                    $table->longText('previewdescription')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'eventcode')) {
                    $table->string('eventcode', 50)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'headergraphic')) {
                    $table->string('headergraphic', 50)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'lastmodified')) {
                    $table->timestamp('lastmodified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('eventname', 50)->nullable();
                $table->date('eventstart')->nullable();
                $table->date('eventend')->nullable();
                $table->integer('minimumstay')->nullable();
                $table->longText('bookingmessage')->nullable();
                $table->longText('description')->nullable();
                $table->string('embeddedvideo', 50)->nullable();
                $table->decimal('extracharge', 10, 0)->nullable();
                $table->decimal('extranightlycharge', 10, 0)->nullable();
                $table->string('poster', 50)->nullable();
                $table->longText('previewdescription')->nullable();
                $table->string('eventcode', 50)->nullable();
                $table->string('headergraphic', 50)->nullable();
                $table->timestamp('lastmodified')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
