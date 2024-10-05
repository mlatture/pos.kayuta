<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $tableName = 'rate_tiers';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'tier')) {
                    $table->string('tier', 30);
                }

                if (!Schema::hasColumn($tableName, 'minimumstay')) {
                    $table->integer('minimumstay')->nullable()->default(1);
                }

                if (!Schema::hasColumn($tableName, 'useflatrate')) {
                    $table->boolean('useflatrate')->nullable()->comment('Use flatrate as the nightly rate if true.');
                }

                if (!Schema::hasColumn($tableName, 'flatrate')) {
                    $table->float('flatrate')->nullable()->comment('Default nightly rate');
                }

                if (!Schema::hasColumn($tableName, 'usedynamic')) {
                    $table->boolean('usedynamic')->nullable()->comment('Use dynamic pricing if true');
                }

                if (!Schema::hasColumn($tableName, 'dynamicincrease')) {
                    $table->float('dynamicincrease')->nullable()->comment('Dollar amount to reduce prices by if occupancy is high');
                }

                if (!Schema::hasColumn($tableName, 'dynamicincreasepercent')) {
                    $table->float('dynamicincreasepercent')->nullable()->comment('Decimal to trigger dynamic increase');
                }

                if (!Schema::hasColumn($tableName, 'dynamicdecrease')) {
                    $table->float('dynamicdecrease')->nullable()->comment('Dollar amount to reduce prices by if occupancy is low');
                }

                if (!Schema::hasColumn($tableName, 'dynamicdecreasepercent')) {
                    $table->float('dynamicdecreasepercent')->nullable()->comment('Decrease when occupancy below this percent');
                }

                if (!Schema::hasColumn($tableName, 'lastminuteincrease')) {
                    $table->float('lastminuteincrease')->nullable()->comment('Increase rate for last minute guests');
                }

                if (!Schema::hasColumn($tableName, 'lastminutedays')) {
                    $table->integer('lastminutedays')->nullable()->comment('Days until reservation to add lastminuteincrease');
                }

                if (!Schema::hasColumn($tableName, 'earlybookingincrease')) {
                    $table->float('earlybookingincrease')->nullable()->comment('Increase rates for very advanced booking');
                }

                if (!Schema::hasColumn($tableName, 'earlybookingdays')) {
                    $table->integer('earlybookingdays')->nullable()->comment('Trigger in days for early booking increase');
                }

                if (!Schema::hasColumn($tableName, 'weeklyrate')) {
                    $table->float('weeklyrate')->nullable()->comment('Weekly rate');
                }

                if (!Schema::hasColumn($tableName, 'monthlyrate')) {
                    $table->float('monthlyrate')->nullable()->comment('Monthly rate');
                }

                if (!Schema::hasColumn($tableName, 'seasonalrate')) {
                    $table->float('seasonalrate')->nullable()->comment('Seasonal rate');
                }

                if (!Schema::hasColumn($tableName, 'sundayrate')) {
                    $table->float('sundayrate')->nullable()->comment('Base rate for Sunday');
                }

                if (!Schema::hasColumn($tableName, 'mondayrate')) {
                    $table->float('mondayrate')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'tuesdayrate')) {
                    $table->float('tuesdayrate')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'wednesdayrate')) {
                    $table->float('wednesdayrate')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'thursdayrate')) {
                    $table->float('thursdayrate')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'fridayrate')) {
                    $table->float('fridayrate')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'saturdayrate')) {
                    $table->float('saturdayrate')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'orderby')) {
                    $table->integer('orderby')->nullable()->comment('Order the drop down for the admin screen');
                }

                if (!Schema::hasColumn($tableName, 'lastmodified')) {
                    $table->timestamp('lastmodified')->nullable()->useCurrent()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
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
                $table->string('tier', 30);
                $table->integer('minimumstay')->nullable()->default(1);
                $table->boolean('useflatrate')->nullable()->comment('Use flatrate as the nightly rate if true.');
                $table->float('flatrate')->nullable()->comment('Default nightly rate');
                $table->boolean('usedynamic')->nullable()->comment('Use dynamic pricing if true');
                $table->float('dynamicincrease')->nullable()->comment('Dollar amount to reduce prices by if occupancy is high');
                $table->float('dynamicincreasepercent')->nullable()->comment('Decimal to trigger dynamic increase');
                $table->float('dynamicdecrease')->nullable()->comment('Dollar amount to reduce prices by if occupancy is low');
                $table->float('dynamicdecreasepercent')->nullable()->comment('Decrease when occupancy below this percent');
                $table->float('lastminuteincrease')->nullable()->comment('Increase rate for last minute guests');
                $table->integer('lastminutedays')->nullable()->comment('Days until reservation to add lastminuteincrease');
                $table->float('earlybookingincrease')->nullable()->comment('Increase rates for very advanced booking');
                $table->integer('earlybookingdays')->nullable()->comment('Trigger in days for early booking increase');
                $table->float('weeklyrate')->nullable()->comment('Weekly rate');
                $table->float('monthlyrate')->nullable()->comment('Monthly rate');
                $table->float('seasonalrate')->nullable()->comment('Seasonal rate');
                $table->float('sundayrate')->nullable()->comment('Base rate for Sunday');
                $table->float('mondayrate')->nullable();
                $table->float('tuesdayrate')->nullable();
                $table->float('wednesdayrate')->nullable();
                $table->float('thursdayrate')->nullable();
                $table->float('fridayrate')->nullable();
                $table->float('saturdayrate')->nullable();
                $table->integer('orderby')->nullable()->comment('Order the drop down for the admin screen');
                $table->timestamp('lastmodified')->nullable()->useCurrent()->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('rate_tiers');
    }
};
