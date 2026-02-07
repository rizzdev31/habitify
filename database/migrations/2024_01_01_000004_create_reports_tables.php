<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Main Reports Table
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelapor_id')->constrained('users')->cascadeOnDelete();
            $table->enum('jenis', ['pelanggaran', 'apresiasi', 'konseling']);
            $table->text('laporan_text');
            $table->enum('status', ['pending', 'approved', 'rejected', 'processed'])->default('pending');
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->text('validation_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'jenis']);
            $table->index(['pelapor_id', 'created_at']);
        });

        // Report Preprocessing Results
        Schema::create('report_preprocessing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->text('text_original');
            $table->text('text_cleaned');
            $table->json('tokens'); // Array of tokens after tokenization
            $table->json('tokens_stemmed'); // Array after stemming
            $table->json('detected_codes'); // Array of detected P/A/G codes
            $table->json('detected_entities')->nullable(); // Detected santri names
            $table->decimal('confidence_score', 3, 2)->default(0); // 0.00 - 1.00
            $table->json('matching_details')->nullable(); // Detailed matching info
            $table->timestamps();

            $table->index('report_id');
        });

        // Report Entities (Santri involved)
        Schema::create('report_entities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('santri_id')->constrained('santri_profiles')->cascadeOnDelete();
            $table->enum('role', ['pelaku', 'korban', 'terlibat']);
            $table->string('detected_name')->nullable(); // Name as detected in text
            $table->decimal('match_confidence', 3, 2)->default(1); // 0.00 - 1.00
            $table->boolean('is_confirmed')->default(false); // Confirmed by BK
            $table->timestamps();

            $table->index(['report_id', 'santri_id']);
            $table->index(['santri_id', 'role']);
        });

        // Report Matches (Keyword matches)
        Schema::create('report_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->string('kata_ditemukan', 100);
            $table->string('kata_stem', 100);
            $table->string('kode_referensi', 10); // P001, A001, G001
            $table->enum('tipe', ['pelanggaran', 'apresiasi', 'konselor']);
            $table->integer('position')->nullable(); // Position in text
            $table->timestamps();

            $table->index(['report_id', 'kode_referensi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_matches');
        Schema::dropIfExists('report_entities');
        Schema::dropIfExists('report_preprocessing');
        Schema::dropIfExists('reports');
    }
};