<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->longText('content');
            $table->enum('category', ['info', 'meeting', 'holiday', 'activity', 'other'])->default('info');
            $table->boolean('is_pinned')->default(false);
            $table->string('attachment')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('letter_type', ['permission', 'leave', 'assignment', 'field_duty', 'work_certificate', 'other']);
            $table->string('letter_number')->unique()->nullable();
            $table->string('subject');
            $table->longText('content')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('company_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // uploader
            $table->enum('category', ['sop', 'regulation', 'sk', 'contract', 'other']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->integer('download_count')->default(0);
            $table->boolean('is_public')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // leave_approved, attendance_late, etc.
            $table->string('title');
            $table->text('message');
            $table->string('url')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->default('blue');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('company_documents');
        Schema::dropIfExists('letters');
        Schema::dropIfExists('announcements');
    }
};
