<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('profile_photo')->nullable()->after('phone');
            $table->enum('account_status', ['active', 'inactive', 'suspended'])->default('active')->after('profile_photo');
            $table->enum('user_type', ['employee', 'customer', 'admin'])->default('customer')->after('account_status');
            $table->string('otp_code')->nullable()->after('user_type');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'profile_photo',
                'account_status',
                'user_type',
                'otp_code',
                'otp_expires_at',
                'deleted_at',
            ]);
        });
    }
};
