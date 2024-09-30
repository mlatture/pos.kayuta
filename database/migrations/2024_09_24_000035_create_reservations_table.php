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
        Schema::create('reservations', static function (Blueprint $table) {
            $table->id();
            $table->string('cartid', 20);
            $table->string('source', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->date('createdate')->nullable();
            $table->string('createdby', 50)->nullable();
            $table->string('fname', 50)->nullable();
            $table->string('lname', 50)->nullable();
            $table->string('customernumber', 20)->nullable();
            $table->string('customertype', 30)->default('weekender');
            $table->string('siteid', 10)->nullable();
            $table->dateTime('cid')->nullable();
            $table->dateTime('cod')->nullable();
            $table->float('total');
            $table->float('subtotal');
            $table->float('taxrate')->nullable();
            $table->float('totaltax')->nullable();
            $table->string('siteclass', 50);
            $table->integer('nights');
            $table->float('extracharge')->nullable();
            $table->float('base');
            $table->float('rateadjustment');
            $table->string('sitelock', 30)->nullable();
            $table->dateTime('checkedin')->nullable();
            $table->dateTime('checkedout')->nullable();
            $table->string('discountcode', 30)->nullable();
            $table->float('discount')->nullable();
            $table->float('totalcharges')->nullable();
            $table->float('totalpayments')->nullable();
            $table->float('balance')->nullable();
            $table->integer('adults')->nullable();
            $table->integer('children')->nullable();
            $table->integer('pets')->nullable();
            $table->string('rigtype', 30)->nullable();
            $table->integer('riglength')->nullable();
            $table->longText('comments')->nullable();
            $table->string('xconfnum', 30);
            $table->string('rid', 30)->nullable()->comment('Referrer id');
            $table->unsignedInteger('receipt');
            $table->unsignedInteger('organization_id')->nullable();
            $table->timestamp('lastmodified')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrent()->nullable();
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
        Schema::dropIfExists('reservations');
    }
};
