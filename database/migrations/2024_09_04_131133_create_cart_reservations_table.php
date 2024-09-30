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
        Schema::create('cart_reservations', static function (Blueprint $table) {
            $table->id();
            $table->dateTime('cid');
            $table->dateTime('cod');
            $table->string('customernumber', 50)->nullable();
            $table->string('cartid', 20);
            $table->string('siteid', 20);
            $table->float('base')->comment('Base nightly rate before adjustments');
            $table->float('rateadjustment')->default(0)->comment('Adjustment from base.');
            $table->float('extracharge')->default(0)->comment('Extra charges for an event');
            $table->integer('riglength')->nullable();
            $table->string('sitelock', 10);
            $table->integer('nights');
            $table->string('siteclass', 50);
            $table->float('taxrate');
            $table->float('totaltax')->default(0)->comment('Tax rate * subtotal');
            $table->string('description', 100);
            $table->text('events')->nullable()->comment('Events during stay');
            $table->float('subtotal')->nullable()->comment('Calculated base + rate adjustment - discount');
            $table->float('total')->comment('Calculated subtotal + tax');
            $table->string('email', 100)->nullable();
            $table->string('rid', 30)->comment('Referrer or Affiliate ID');
            $table->string('discountcode', 20)->nullable();
            $table->float('discount')->nullable()->comment('Amount of discount');
            $table->dateTime('holduntil')->default(now());
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
        Schema::dropIfExists('cart_reservations');
    }
};
