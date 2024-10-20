<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = 'tax_types';
        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'organization_id')) {
                    $table->integer('organization_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'title')) {
                    $table->string('title')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'tax_type')) {
                    $table->string('tax_type')->nullable()->comment('percentage, fixed_amount');
                }
                if (!Schema::hasColumn($tableName, 'tax')) {
                    $table->double('tax')->default(0);
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->integer('organization_id')->nullable();
                $table->string('title')->nullable();
                $table->string('tax_type')->nullable()->comment('percentage, fixed_amount');
                $table->double('tax')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tax_types');
    }
};
