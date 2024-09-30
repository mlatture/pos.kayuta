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
        Schema::create('payments', static function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 8, 4);
            $table->integer('organization_id')->nullable();
            $table->string('cartid', 255)->nullable();
            $table->integer('receipt')->default(0);
            $table->string('method', 255)->nullable();
            $table->string('customernumber', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->float('payment')->default(0);
            $table->foreignId('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->dateTime('createdate')->default(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('payments');
    }
};
