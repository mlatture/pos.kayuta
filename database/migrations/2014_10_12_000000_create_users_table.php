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
        $tableName = 'users';
//        user schema
        if (Schema::hasTable($tableName)) {

            Schema::table($tableName, static function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'organization_id')) {
                    $table->integer('organization_id')->nullable();
                }

             
                if (!Schema::hasColumn($tableName, 'f_name')) {
                    $table->string('f_name', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'l_name')) {
                    $table->string('l_name', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'phone')) {
                    $table->string('phone', 25)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'image')) {
                    $table->string('image', 30)->default('def.png');
                }
                if (!Schema::hasColumn($tableName, 'email')) {
                    $table->string('email', 80)->unique();
                }
                if (!Schema::hasColumn($tableName, 'email_verified_at')) {
                    $table->timestamp('email_verified_at')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'password')) {
                    $table->string('password', 80);
                }
                if (!Schema::hasColumn($tableName, 'remember_token')) {
                    $table->string('remember_token', 100)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'otp')) {
                    $table->string('otp', 100)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'ip_address')) {
                    $table->string('ip_address', 100)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'street_address')) {
                    $table->string('street_address', 250)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'country')) {
                    $table->string('country', 50)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'city')) {
                    $table->string('city', 50)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'zip')) {
                    $table->string('zip', 20)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'house_no')) {
                    $table->string('house_no', 50)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'apartment_no')) {
                    $table->string('apartment_no', 50)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'cm_firebase_token')) {
                    $table->string('cm_firebase_token', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'is_active')) {
                    $table->boolean('is_active')->default(true);
                }
                if (!Schema::hasColumn($tableName, 'payment_card_last_four')) {
                    $table->string('payment_card_last_four', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'payment_card_brand')) {
                    $table->string('payment_card_brand', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'payment_card_fawry_token')) {
                    $table->text('payment_card_fawry_token')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'login_medium')) {
                    $table->string('login_medium', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'social_id')) {
                    $table->string('social_id', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'facebook_id')) {
                    $table->string('facebook_id', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'google_id')) {
                    $table->string('google_id', 255)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'is_phone_verified')) {
                    $table->boolean('is_phone_verified')->default(false);
                }
                if (!Schema::hasColumn($tableName, 'temporary_token')) {
                    $table->string('temporary_token', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'is_email_verified')) {
                    $table->boolean('is_email_verified')->default(false);
                }
                if (!Schema::hasColumn($tableName, 'wallet_balance')) {
                    $table->double('wallet_balance', 8, 2)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'loyalty_point')) {
                    $table->double('loyalty_point', 8, 2)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'stripe_customer_id')) {
                    $table->string('stripe_customer_id', 191)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'liabilty_path')) {
                    $table->string('liabilty_path', 255)->nullable();
                }
            });
        } else {
            Schema::create('users', static function (Blueprint $table) {
                $table->id();
                $table->integer('organization_id')->nullable();
//                $table->integer('testing_123')->nullable();
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
