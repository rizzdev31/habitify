<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajar_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nip', 30)->nullable();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('no_hp', 20)->nullable();
            $table->string('bidang_studi')->nullable();
            $table->string('jabatan')->nullable();
            $table->date('tanggal_bergabung')->nullable();
            $table->enum('status', ['aktif', 'non_aktif'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });

        Schema::create('bk_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nip', 30)->nullable();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('no_hp', 20)->nullable();
            $table->string('spesialisasi')->nullable(); // e.g., "Konseling Remaja"
            $table->string('sertifikasi')->nullable();
            $table->date('tanggal_bergabung')->nullable();
            $table->enum('status', ['aktif', 'non_aktif'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });

        Schema::create('wali_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('santri_id')->constrained('santri_profiles')->cascadeOnDelete();
            $table->string('nama_lengkap');
            $table->string('hubungan', 50); // Ayah, Ibu, Wali
            $table->string('no_hp', 20)->nullable();
            $table->string('no_whatsapp', 20)->nullable();
            $table->string('pekerjaan')->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('santri_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wali_profiles');
        Schema::dropIfExists('bk_profiles');
        Schema::dropIfExists('pengajar_profiles');
    }
};