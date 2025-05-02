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
        Schema::table('categories', function (Blueprint $table) {
            $table->string('quick_books_account_name')->nullable()->after('name');
            $table->string('account_type')->nullable()->after('quick_books_account_name');
            $table->string('notes')->nullable()->after('account_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['quick_books_account_name', 'account_type', 'notes', 'organization_id']);
        });
    }
};
