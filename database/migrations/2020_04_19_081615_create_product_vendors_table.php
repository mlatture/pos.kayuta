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
        Schema::create('product_vendors', static function (Blueprint $table) {
            $table->id();
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
