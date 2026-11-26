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
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->string('verification_token')->unique()->index();
            $table->string('letter_number')->unique();
            $table->string('title');
            $table->enum('category', [
                'surat_izin',
                'surat_tugas',
                'surat_undangan',
                'surat_keterangan',
                'lainnya'
            ])->default('surat_izin');
            $table->date('letter_date');
            $table->string('event_name');
            $table->string('draft_file_path');
            $table->string('signed_file_path')->nullable();
            $table->string('file_hash', 64)->nullable()->index();
            $table->enum('status', ['pending', 'approved', 'rejected', 'revoked'])->default('pending')->index();
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_or_revoke_reason')->nullable();
            $table->integer('stamp_page')->default(1);
            $table->enum('stamp_position', ['bottom_right', 'bottom_left', 'bottom_center'])->default('bottom_right');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
