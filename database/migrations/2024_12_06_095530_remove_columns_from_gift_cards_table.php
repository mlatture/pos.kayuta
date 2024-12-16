<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveColumnsFromGiftCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gift_cards', function (Blueprint $table) {
            // Removing the unnecessary columns
            $table->dropColumn([
                'organization_id',
                'user_email',
                'discount',
                'start_date',
                'min_purchase',
                'max_discount',
                'limit',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gift_cards', function (Blueprint $table) {
            // Adding back the columns in case you want to roll back
            $table->integer('organization_id')->nullable();
            $table->string('user_email')->nullable();
            $table->decimal('discount', 8, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->decimal('min_purchase', 8, 2)->nullable();
            $table->decimal('max_discount', 8, 2)->nullable();
            $table->integer('limit')->nullable();
        });
    }
}
