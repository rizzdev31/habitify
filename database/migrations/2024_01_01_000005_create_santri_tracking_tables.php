<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Santri Points Summary
        Schema::create('santri_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri_profiles')->cascadeOnDelete();
            $table->integer('total_poin_pelanggaran')->default(0);
            $table->integer('total_poin_apresiasi')->default(0);
            $table->string('current_konsekuensi_kode', 10)->nullable(); // Current threshold level
            $table->string('current_reward_kode', 10)->nullable();
            $table->timestamp('last_konsekuensi_at')->nullable();
            $table->timestamp('last_reward_at')->nullable();
            $table->timestamps();

            $table->unique('santri_id');
            $table->index(['total_poin_pelanggaran', 'total_poin_apresiasi']);
        });

        // Santri Facts (Working Memory for Expert System)
        Schema::create('santri_facts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri_profiles')->cascadeOnDelete();
            $table->string('fact_code', 10); // P001, G010, A001, etc.
            $table->enum('fact_type', ['pelanggaran', 'apresiasi', 'konselor']);
            $table->foreignId('source_report_id')->nullable()->constrained('reports')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable(); // Auto-deactivate after X months
            $table->timestamps();

            $table->index(['santri_id', 'fact_code', 'is_active']);
            $table->index(['fact_code', 'is_active']);
        });

        // Santri Violations History
        Schema::create('santri_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri_profiles')->cascadeOnDelete();
            $table->foreignId('report_id')->nullable()->constrained()->nullOnDelete();
            $table->string('pelanggaran_kode', 10);
            $table->string('pelanggaran_nama', 100);
            $table->integer('poin');
            $table->text('konsekuensi')->nullable();
            $table->boolean('konsekuensi_selesai')->default(false);
            $table->timestamp('konsekuensi_selesai_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['santri_id', 'created_at']);
        });

        // Santri Appreciations History
        Schema::create('santri_appreciations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri_profiles')->cascadeOnDelete();
            $table->foreignId('report_id')->nullable()->constrained()->nullOnDelete();
            $table->string('apresiasi_kode', 10);
            $table->string('apresiasi_nama', 100);
            $table->integer('poin');
            $table->text('reward')->nullable();
            $table->boolean('reward_diberikan')->default(false);
            $table->timestamp('reward_diberikan_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['santri_id', 'created_at']);
        });

        // Santri Counseling History
        Schema::create('santri_counseling', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri_profiles')->cascadeOnDelete();
            $table->foreignId('bk_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('report_id')->nullable()->constrained()->nullOnDelete();
            $table->string('diagnosis_kode', 10)->nullable();
            $table->string('diagnosis_nama')->nullable();
            $table->date('tanggal_konseling');
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->text('catatan_konseling');
            $table->text('rekomendasi_tindak_lanjut')->nullable();
            $table->date('jadwal_follow_up')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('completed');
            $table->timestamps();

            $table->index(['santri_id', 'tanggal_konseling']);
            $table->index(['bk_id', 'tanggal_konseling']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('santri_counseling');
        Schema::dropIfExists('santri_appreciations');
        Schema::dropIfExists('santri_violations');
        Schema::dropIfExists('santri_facts');
        Schema::dropIfExists('santri_points');
    }
};