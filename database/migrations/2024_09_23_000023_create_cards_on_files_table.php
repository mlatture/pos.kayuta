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
        Schema::create('cards_on_files', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->string('customernumber', 30);
            $table->string('cartid', 30);
            $table->integer('receipt');
            $table->string('email', 100);
            $table->string('xmaskedcardnumber', 30);
            $table->string('method', 50);
            $table->string('xtoken', 50);
            $table->text('gateway_response');
            $table->dateTime('createdate')->default(now());
            $table->dateTime('lastmodified')->default(now())->onUpdate(now());
            $table->timestamps(); // This adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('cards_on_files');
    }
};
