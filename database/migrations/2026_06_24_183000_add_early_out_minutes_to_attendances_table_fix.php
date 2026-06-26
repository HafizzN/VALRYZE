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
        if (!Schema::hasColumn('attendances', 'early_out_minutes')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->integer('early_out_minutes')->default(0)->after('late_minutes');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('attendances', 'early_out_minutes')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn('early_out_minutes');
            });
        }
    }
};
