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
        $tableName = 'cards_on_files';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'customernumber')) {
                    $table->string('customernumber', 30)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'cartid')) {
                    $table->string('cartid', 30)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'receipt')) {
                    $table->integer('receipt')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'email')) {
                    $table->string('email', 100)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'xmaskedcardnumber')) {
                    $table->string('xmaskedcardnumber', 30)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'method')) {
                    $table->string('method', 50)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'xtoken')) {
                    $table->string('xtoken', 50)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'gateway_response')) {
                    $table->text('gateway_response')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'createdate')) {
                    $table->dateTime('createdate')->default(now());
                }
                if (!Schema::hasColumn($tableName, 'lastmodified')) {
                    $table->dateTime('lastmodified')->default(now())->onUpdate(now());
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id(); // Auto-incrementing ID
                $table->string('customernumber', 30)->nullable();
                $table->string('cartid', 30)->nullable();
                $table->integer('receipt')->nullable();
                $table->string('email', 100)->nullable();
                $table->string('xmaskedcardnumber', 30)->nullable();
                $table->string('method', 50)->nullable();
                $table->string('xtoken', 50)->nullable();
                $table->text('gateway_response')->nullable();
                $table->dateTime('createdate')->default(now());
                $table->dateTime('lastmodified')->default(now())->onUpdate(now());
                $table->timestamps(); // This adds created_at and updated_at columns
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
        Schema::dropIfExists('cards_on_files');
    }
};
