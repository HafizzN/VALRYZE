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
            $table->decimal('basic_salary', 15, 2)->nullable()->after('fcm_token');
            $table->decimal('allowance', 15, 2)->nullable()->after('basic_salary');
            $table->decimal('bpjs_deduction', 15, 2)->nullable()->after('allowance');
            $table->decimal('tax_deduction', 15, 2)->nullable()->after('bpjs_deduction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['basic_salary', 'allowance', 'bpjs_deduction', 'tax_deduction']);
        });
    }
};
