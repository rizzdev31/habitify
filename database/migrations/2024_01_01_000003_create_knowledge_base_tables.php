<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Knowledge Base: Pelanggaran (P001-P016)
        Schema::create('kb_pelanggaran', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique(); // P001, P002, etc.
            $table->string('nama', 100);
            $table->integer('poin')->default(0);
            $table->text('konsekuensi')->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('kode');
        });

        // Knowledge Base: Apresiasi (A001-A003)
        Schema::create('kb_apresiasi', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique(); // A001, A002, etc.
            $table->string('nama', 100);
            $table->integer('poin')->default(0);
            $table->text('reward')->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('kode');
        });

        // Knowledge Base: Konselor / Gangguan Mental (G001-G019)
        Schema::create('kb_konselor', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique(); // G001, G002, etc.
            $table->string('nama', 100);
            $table->text('deskripsi')->nullable();
            $table->text('gejala')->nullable(); // JSON array of symptoms
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('kode');
        });

        // Knowledge Base: Konsekuensi by Threshold (K001-K010)
        Schema::create('kb_konsekuensi', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique(); // K001, K002, etc.
            $table->string('nama', 100);
            $table->integer('threshold_min'); // Minimum point to trigger
            $table->integer('threshold_max')->nullable(); // Maximum point (null = infinity)
            $table->text('tindakan');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['threshold_min', 'threshold_max']);
        });

        // Knowledge Base: Reward by Threshold (R001-R005)
        Schema::create('kb_reward', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique(); // R001, R002, etc.
            $table->string('nama', 100);
            $table->integer('threshold_min'); // Minimum point to trigger
            $table->integer('threshold_max')->nullable();
            $table->text('reward');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['threshold_min', 'threshold_max']);
        });

        // Knowledge Base: Diagnosis (DX-A01 to DX-C19)
        Schema::create('kb_diagnosis', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique(); // DX-A01, DX-B01, DX-C01, etc.
            $table->string('nama', 150);
            $table->text('penjelasan');
            $table->text('rekomendasi');
            $table->enum('kategori', ['korban', 'pelaku', 'internal']);
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['kode', 'kategori']);
        });

        // Knowledge Base: Dictionary (Kamus Kata)
        Schema::create('kb_dictionary', function (Blueprint $table) {
            $table->id();
            $table->string('kata', 100); // Stemmed word
            $table->string('kode_referensi', 10); // P001, A001, G001, etc.
            $table->enum('tipe', ['pelanggaran', 'apresiasi', 'konselor']);
            $table->integer('bobot')->default(1); // Weight for matching
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['kata', 'tipe']);
            $table->index('kode_referensi');
        });

        // Knowledge Base: Rules (IF-THEN)
        Schema::create('kb_rules', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique(); // RA-01, RB-01, RC-01, etc.
            $table->string('nama', 150);
            $table->enum('kategori', ['korban', 'pelaku', 'internal']);
            $table->json('conditions'); // Array of condition codes ["G010", "P001", "G001"]
            $table->enum('operator', ['AND', 'OR'])->default('AND');
            $table->integer('min_match')->default(0); // Minimum conditions to match (0 = all)
            $table->string('diagnosis_kode', 10); // FK to kb_diagnosis
            $table->integer('prioritas')->default(0); // Lower = higher priority
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['kategori', 'prioritas']);
            $table->index('diagnosis_kode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kb_rules');
        Schema::dropIfExists('kb_dictionary');
        Schema::dropIfExists('kb_diagnosis');
        Schema::dropIfExists('kb_reward');
        Schema::dropIfExists('kb_konsekuensi');
        Schema::dropIfExists('kb_konselor');
        Schema::dropIfExists('kb_apresiasi');
        Schema::dropIfExists('kb_pelanggaran');
    }
};