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
        Schema::create('users', static function (Blueprint $table) {
            $table->id();
            $table->integer('organization_id')->nullable();
            $table->string('name', 80)->nullable();
            $table->string('f_name', 255)->nullable();
            $table->string('l_name', 191)->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('image', 30)->default('def.png');
            $table->string('email', 80)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 80);
            $table->string('remember_token', 100)->nullable();
            $table->string('otp', 100)->nullable();
            $table->timestamps();
            $table->string('ip_address', 100)->nullable();
            $table->string('street_address', 250)->nullable();
            $table->string('country', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('zip', 20)->nullable();
            $table->string('house_no', 50)->nullable();
            $table->string('apartment_no', 50)->nullable();
            $table->string('cm_firebase_token', 191)->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('payment_card_last_four', 191)->nullable();
            $table->string('payment_card_brand', 191)->nullable();
            $table->text('payment_card_fawry_token')->nullable();
            $table->string('login_medium', 191)->nullable();
            $table->string('social_id', 191)->nullable();
            $table->string('facebook_id', 255)->nullable();
            $table->string('google_id', 255)->nullable();
            $table->boolean('is_phone_verified')->default(false);
            $table->string('temporary_token', 191)->nullable();
            $table->boolean('is_email_verified')->default(false);
            $table->double('wallet_balance', 8, 2)->nullable();
            $table->double('loyalty_point', 8, 2)->nullable();
            $table->string('stripe_customer_id', 191)->nullable();
            $table->string('liabilty_path', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
