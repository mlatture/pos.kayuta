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
        $tableName = 'cart_reservations';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn($tableName, 'cid')) {
                    $table->dateTime('cid')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'cod')) {
                    $table->dateTime('cod')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'customernumber')) {
                    $table->string('customernumber', 50)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'cartid')) {
                    $table->string('cartid', 20)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'siteid')) {
                    $table->string('siteid', 20)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'hookups')) {
                    $table->string('hookups', 20)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'base')) {
                    $table->float('base')->comment('Base nightly rate before adjustments')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'rateadjustment')) {
                    $table->float('rateadjustment')->default(0)->comment('Adjustment from base.')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'extracharge')) {
                    $table->float('extracharge')->default(0)->comment('Extra charges for an event')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'riglength')) {
                    $table->integer('riglength')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'sitelock')) {
                    $table->string('sitelock', 10)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'nights')) {
                    $table->integer('nights')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'siteclass')) {
                    $table->string('siteclass', 50)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'taxrate')) {
                    $table->float('taxrate')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'totaltax')) {
                    $table->float('totaltax')->default(0)->comment('Tax rate * subtotal')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'description')) {
                    $table->string('description', 100)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'events')) {
                    $table->text('events')->nullable()->comment('Events during stay');
                }
                if (!Schema::hasColumn($tableName, 'subtotal')) {
                    $table->float('subtotal')->nullable()->comment('Calculated base + rate adjustment - discount');
                }
                if (!Schema::hasColumn($tableName, 'total')) {
                    $table->float('total')->comment('Calculated subtotal + tax')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'email')) {
                    $table->string('email', 100)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'rid')) {
                    $table->string('rid', 30)->comment('Referrer or Affiliate ID')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'discountcode')) {
                    $table->string('discountcode', 20)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'discount')) {
                    $table->float('discount')->nullable()->comment('Amount of discount');
                }
                if (!Schema::hasColumn($tableName, 'holduntil')) {
                    $table->dateTime('holduntil')->default(now());
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->dateTime('cid')->nullable();
                $table->dateTime('cod')->nullable();
                $table->string('customernumber', 50)->nullable();
                $table->string('hookups')->nullable();
                $table->string('cartid', 20)->nullable();
                $table->string('siteid', 20)->nullable();
                $table->float('base')->comment('Base nightly rate before adjustments')->nullable();
                $table->float('rateadjustment')->default(0)->comment('Adjustment from base.')->nullable();
                $table->float('extracharge')->default(0)->comment('Extra charges for an event')->nullable();
                $table->integer('riglength')->nullable();
                $table->string('sitelock', 10)->nullable();
                $table->integer('nights')->nullable();
                $table->string('siteclass', 50)->nullable();
                $table->float('taxrate')->nullable();
                $table->float('totaltax')->default(0)->comment('Tax rate * subtotal')->nullable();
                $table->string('description', 100)->nullable();
                $table->text('events')->nullable()->comment('Events during stay');
                $table->float('subtotal')->nullable()->comment('Calculated base + rate adjustment - discount');
                $table->float('total')->comment('Calculated subtotal + tax')->nullable();
                $table->string('email', 100)->nullable();
                $table->string('rid', 30)->comment('Referrer or Affiliate ID')->nullable();
                $table->string('discountcode', 20)->nullable();
                $table->float('discount')->nullable()->comment('Amount of discount');
                $table->dateTime('holduntil')->default(now());
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
        Schema::dropIfExists('cart_reservations');
    }
};
