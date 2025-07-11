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
    public function up()
    {
        Schema::dropIfExists('seasonal_renewals');

        Schema::create('seasonal_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade'); // remove
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->boolean('allow_renew')->default(false);

            $table->enum('status', ['pending', 'sent offer', 'sent rejection', 'paid in full', 'paid deposit', 'accepted', 'declined'])->default('pending');
            $table->decimal('initial_rate', 10, 2)->nullable();
            $table->decimal('discount_percent', 5, 2)->nullable()->comment('Enter a discount percentage to be taken off the Rate');
            $table->decimal('discount_amount', 10, 2)->nullable()->comment('Enter a discount amount to be taken off the Rate');
            $table->text('discount_note')->nullable()->comment('This text will be shown to the customer explaining why they get the discount');
            $table->decimal('final_rate', 10, 2)->nullable(); // auto-calculate

            $table->enum('payment_plan', ['paid_in_full', 'monthly_ach', 'monthly_credit'])->nullable();
            $table->unsignedBigInteger('payment_plan_id')->nullable(); // remove
        
            $table->string('selected_payment_method')->nullable(); // remove
            $table->tinyInteger('day_of_month')->nullable()->comment('Any number less than 29');

            $table->decimal('offered_rate', 10, 2)->nullable(); // remove
            $table->boolean('renewed')->default(false); // remove
            $table->date('response_date')->nullable(); // remove
            $table->text('notes')->nullable(); // remove

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('seasonal_renewals');

    }
};
