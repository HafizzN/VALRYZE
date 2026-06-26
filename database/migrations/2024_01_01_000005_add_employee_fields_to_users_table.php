<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Modify existing users table to add HR-related fields
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik')->unique()->nullable()->after('id');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('photo')->nullable()->after('address');
            $table->string('gender')->nullable()->after('photo');
            $table->date('birth_date')->nullable()->after('gender');
            $table->string('birth_place')->nullable()->after('birth_date');
            $table->string('religion')->nullable()->after('birth_place');
            $table->string('marital_status')->nullable()->after('religion');
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete()->after('marital_status');
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete()->after('division_id');
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete()->after('position_id');
            $table->date('join_date')->nullable()->after('shift_id');
            $table->date('resign_date')->nullable()->after('join_date');
            $table->enum('employment_type', ['permanent', 'contract', 'internship', 'freelance'])->default('permanent')->after('resign_date');
            $table->enum('status', ['active', 'inactive', 'resign'])->default('active')->after('employment_type');
            $table->integer('annual_leave_quota')->default(12)->after('status');
            $table->integer('annual_leave_used')->default(0)->after('annual_leave_quota');
            $table->softDeletes();
        });

        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['ktp', 'npwp', 'cv', 'contract', 'bpjs_kes', 'bpjs_naker', 'ijazah', 'other']);
            $table->string('title')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nik', 'phone', 'address', 'photo', 'gender', 'birth_date', 'birth_place',
                'religion', 'marital_status', 'division_id', 'position_id', 'shift_id',
                'join_date', 'resign_date', 'employment_type', 'status',
                'annual_leave_quota', 'annual_leave_used'
            ]);
            $table->dropSoftDeletes();
        });
    }
};
