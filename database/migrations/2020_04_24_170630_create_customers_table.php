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
    public function up(): void
    {
        $tableName = 'customers';

//        customers schemaa

        if (Schema::hasTable($tableName)) {

            Schema::table($tableName, static function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'id')) {
                    $table->id();
                }
                if (!Schema::hasColumn($tableName, 'first_name')) {
                    $table->string('first_name', 20)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'last_name')) {
                    $table->string('last_name', 20)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'email')) {
                    $table->string('email')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'phone')) {
                    $table->string('phone')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'address')) {
                    $table->string('address')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'avatar')) {
                    $table->string('avatar')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'organization_id')) {
                    $table->integer('organization_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamps();
                }
                if (!Schema::hasColumn($tableName, 'updated_at')) {
                    $table->timestamps();
                }
            });
        } else {
            Schema::create($tableName, static function (Blueprint $table) {
                $table->id();
                $table->string('first_name', 20)->nullable();
                $table->string('last_name', 20)->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('address')->nullable();
                $table->string('avatar')->nullable();
                $table->integer('organization_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->timestamps();

                //$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('user_id', 'customers_user_id_foreign');
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
        Schema::dropIfExists('customers');
    }
};
