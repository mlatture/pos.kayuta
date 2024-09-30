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
        Schema::create('rate_tiers', static function (Blueprint $table) {
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
