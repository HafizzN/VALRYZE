<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('leave_type', ['annual', 'maternity', 'paternity', 'wedding', 'big_leave', 'sick', 'other']);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days');
            $table->text('reason');
            $table->string('attachment')->nullable();
            // Maternity specific
            $table->string('child_name')->nullable();
            $table->date('child_birth_date')->nullable();
            // Wedding specific
            $table->date('wedding_date')->nullable();
            $table->enum('status', ['pending', 'approved_manager', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by_manager')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by_hrd')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('manager_approved_at')->nullable();
            $table->timestamp('hrd_approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('permission_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('permission_type', ['sick', 'family', 'field_duty', 'personal']);
            $table->date('date');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('reason');
            $table->string('attachment')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_requests');
        Schema::dropIfExists('leave_requests');
    }
};
