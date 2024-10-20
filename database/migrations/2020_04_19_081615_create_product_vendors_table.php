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
        $tableName = 'product_vendors';
//        product vendor schema

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, static function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id(); // Primary key
                }
                if (!Schema::hasColumn($tableName, 'name')) {
                    $table->string('name', 191);
                }
                if (!Schema::hasColumn($tableName, 'address_1')) {
                    $table->string('address_1', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'address_2')) {
                    $table->string('address_2', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'city')) {
                    $table->string('city', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'state')) {
                    $table->string('state', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'zip')) {
                    $table->string('zip', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'country')) {
                    $table->string('country', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'contact_name')) {
                    $table->string('contact_name', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'email')) {
                    $table->string('email', 191)->unique();
                }
                if (!Schema::hasColumn($tableName, 'work_phone')) {
                    $table->string('work_phone', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'mobile_phone')) {
                    $table->string('mobile_phone', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'fax')) {
                    $table->string('fax', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'notes')) {
                    $table->text('notes')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'organization_id')) {
                    $table->unsignedInteger('organization_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, static function (Blueprint $table) {
                $table->id(); // Primary key
                $table->string('name', 191);
                $table->string('address_1', 191)->nullable();
                $table->string('address_2', 191)->nullable();
                $table->string('city', 191)->nullable();
                $table->string('state', 191)->nullable();
                $table->string('zip', 191)->nullable();
                $table->string('country', 191)->nullable();
                $table->string('contact_name', 191)->nullable();
                $table->string('email', 191)->unique();
                $table->string('work_phone', 191)->nullable();
                $table->string('mobile_phone', 191)->nullable();
                $table->string('fax', 191)->nullable();
                $table->text('notes')->nullable();
                $table->unsignedInteger('organization_id')->nullable();
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
        Schema::dropIfExists('product_vendors');
    }
};
