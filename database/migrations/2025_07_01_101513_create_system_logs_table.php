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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();

            $table->timestamp('created_at')->useCurrent(); // auto date
            $table->string('transaction_type')->nullable()->comment('e.g., Scheduled Job, Inventory Update, Online Booking, Store Sale');

            $table->decimal('sale_amount', 10, 2)->nullable();
            $table->enum('status', ['Success', 'Failed'])->nullable();
            $table->enum('payment_type', ['ACH', 'Credit'])->nullable();

            $table->string('confirmation_number')->nullable();

            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();

            $table->unsignedBigInteger('user_id')->nullable()->comment('ID of staff, cron, or system user');
            $table->text('description')->nullable();

            $table->json('before')->nullable();
            $table->json('after')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_logs');
    }
};
