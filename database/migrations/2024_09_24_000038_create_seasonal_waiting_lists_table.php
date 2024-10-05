<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $tableName = 'seasonal_waiting_lists';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'first_name')) {
                    $table->string('first_name', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'last_name')) {
                    $table->string('last_name', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'email')) {
                    $table->string('email', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'address')) {
                    $table->text('address')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'city')) {
                    $table->string('city', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'state')) {
                    $table->string('state', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'zip')) {
                    $table->string('zip', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'no_of_adults')) {
                    $table->string('no_of_adults', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'no_of_children_below_thirteen')) {
                    $table->string('no_of_children_below_thirteen', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'no_of_children_above_twelve')) {
                    $table->string('no_of_children_above_twelve', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'camper_type')) {
                    $table->string('camper_type', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'camper_year')) {
                    $table->string('camper_year', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'length_of_camper')) {
                    $table->string('length_of_camper', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'camped_before')) {
                    $table->boolean('camped_before')->default(0);
                }

                if (!Schema::hasColumn($tableName, 'last_visit')) {
                    $table->string('last_visit', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'hear_about_us')) {
                    $table->string('hear_about_us', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'reference')) {
                    $table->string('reference', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }

                if (!Schema::hasColumn($tableName, 'updated_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('first_name', 255)->nullable();
                $table->string('last_name', 255)->nullable();
                $table->string('email', 255)->nullable();
                $table->text('address')->nullable();
                $table->string('city', 255)->nullable();
                $table->string('state', 255)->nullable();
                $table->string('zip', 255)->nullable();
                $table->string('no_of_adults', 255)->nullable();
                $table->string('no_of_children_below_thirteen', 255)->nullable();
                $table->string('no_of_children_above_twelve', 255)->nullable();
                $table->string('camper_type', 255)->nullable();
                $table->string('camper_year', 255)->nullable();
                $table->string('length_of_camper', 255)->nullable();
                $table->boolean('camped_before')->default(0);
                $table->string('last_visit', 255)->nullable();
                $table->string('hear_about_us', 255)->nullable();
                $table->string('reference', 255)->nullable();
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
        Schema::dropIfExists('seasonal_waiting_lists');
    }
};
