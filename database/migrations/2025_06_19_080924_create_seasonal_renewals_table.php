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
        Schema::create('seasonal_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->decimal('offered_rate', 10, 2)->nullable();
            $table->boolean('renewed')->default(false);
            $table->date('response_date')->nullable();
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->text('notes')->nullable();        
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
        Schema::dropIfExists('seasonal_renewals');
    }
};
