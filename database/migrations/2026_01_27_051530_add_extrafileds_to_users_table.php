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
            //
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('dob')->nullable();
            $table->string('country')->nullable();
            $table->string('company_name')->nullable();
            $table->string('Chamber_of_Commerce_kvk_number')->nullable();
            $table->string('Chamber_of_Commerce')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn(['first_name', 'last_name', 'phone', 'address', 'gender', 'dob', 'country', 'company_name', 'Chamber_of_Commerce_kvk_number', 'Chamber_of_Commerce']);
        });
    }
};
