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
        Schema::table('attendances', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['user_id']);
            
            // Drop the old unique constraint
            $table->dropUnique(['user_id', 'date']);
            
            // Add the new unique constraint
            $table->unique(['user_id', 'date', 'shift_id']);
            
            // Recreate foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['user_id']);
            
            // Drop the new unique constraint
            $table->dropUnique(['user_id', 'date', 'shift_id']);
            
            // Restore the old unique constraint
            $table->unique(['user_id', 'date']);
            
            // Recreate foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
