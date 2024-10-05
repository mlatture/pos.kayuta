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
        $tableName = 'reservations';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'cartid')) {
                    $table->string('cartid', 20)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'source')) {
                    $table->string('source', 30)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'email')) {
                    $table->string('email', 100)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'createdate')) {
                    $table->date('createdate')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'createdby')) {
                    $table->string('createdby', 50)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'fname')) {
                    $table->string('fname', 50)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'lname')) {
                    $table->string('lname', 50)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'customernumber')) {
                    $table->string('customernumber', 20)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'customertype')) {
                    $table->string('customertype', 30)->default('weekender');
                }

                if (!Schema::hasColumn($tableName, 'siteid')) {
                    $table->string('siteid', 10)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'cid')) {
                    $table->dateTime('cid')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'cod')) {
                    $table->dateTime('cod')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'total')) {
                    $table->float('total')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'subtotal')) {
                    $table->float('subtotal')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'taxrate')) {
                    $table->float('taxrate')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'totaltax')) {
                    $table->float('totaltax')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'siteclass')) {
                    $table->string('siteclass', 50);
                }

                if (!Schema::hasColumn($tableName, 'nights')) {
                    $table->integer('nights')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'extracharge')) {
                    $table->float('extracharge')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'base')) {
                    $table->float('base')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'rateadjustment')) {
                    $table->float('rateadjustment')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'sitelock')) {
                    $table->string('sitelock', 30)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'checkedin')) {
                    $table->dateTime('checkedin')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'checkedout')) {
                    $table->dateTime('checkedout')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'discountcode')) {
                    $table->string('discountcode', 30)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'discount')) {
                    $table->float('discount')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'totalcharges')) {
                    $table->float('totalcharges')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'totalpayments')) {
                    $table->float('totalpayments')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'balance')) {
                    $table->float('balance')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'adults')) {
                    $table->integer('adults')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'children')) {
                    $table->integer('children')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'pets')) {
                    $table->integer('pets')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'rigtype')) {
                    $table->string('rigtype', 30)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'riglength')) {
                    $table->integer('riglength')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'comments')) {
                    $table->longText('comments')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'xconfnum')) {
                    $table->string('xconfnum', 30);
                }

                if (!Schema::hasColumn($tableName, 'rid')) {
                    $table->string('rid', 30)->nullable()->comment('Referrer id');
                }

                if (!Schema::hasColumn($tableName, 'receipt')) {
                    $table->unsignedInteger('receipt')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'organization_id')) {
                    $table->unsignedInteger('organization_id')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'lastmodified')) {
                    $table->timestamp('lastmodified')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrent()->nullable();
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
                $table->string('cartid', 20)->nullable();
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
                $table->float('total')->nullable();
                $table->float('subtotal')->nullable();
                $table->float('taxrate')->nullable();
                $table->float('totaltax')->nullable();
                $table->string('siteclass', 50);
                $table->integer('nights')->nullable();
                $table->float('extracharge')->nullable();
                $table->float('base')->nullable();
                $table->float('rateadjustment')->nullable();
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
                $table->unsignedInteger('receipt')->nullable();
                $table->unsignedInteger('organization_id')->nullable();
                $table->timestamp('lastmodified')->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrent()->nullable();
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
        Schema::dropIfExists('reservations');
    }
};
