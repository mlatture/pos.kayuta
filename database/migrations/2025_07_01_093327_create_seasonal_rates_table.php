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
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('file');
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

        Schema::create('seasonal_rates', function (Blueprint $table) {
            $table->id();
            $table->string('rate_name');
            $table->decimal('rate_price', 8, 2)->default(0);
            $table->decimal('deposit_amount', 8, 2)->default(0);
            $table->decimal('early_pay_discount', 8, 2)->default(0);
            $table->decimal('full_payment_discount', 8, 2)->default(0);
            $table->date('payment_plan_starts')->nullable();
            $table->date('final_payment_due')->nullable();
            $table->foreignId('template_id')->constrained('document_templates')->nullable();
            $table->boolean('applies_to_all')->default(true);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->json('seasonal')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()

    {
        if (Schema::hasColumn('users', 'seasonal')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('seasonal');
            });
        }
    
    

        Schema::dropIfExists('seasonal_rates');
        Schema::dropIfExists('document_templates');
    }
};
