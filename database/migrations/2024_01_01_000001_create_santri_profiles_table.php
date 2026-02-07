<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('santri_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nisn', 20)->unique();
            $table->string('nama_lengkap');
            $table->string('nama_panggilan', 100)->nullable(); // For NER matching
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('no_whatsapp_wali', 20)->nullable(); // For WhatsApp notification
            $table->string('nama_wali')->nullable();
            $table->string('kelas', 20)->nullable();
            $table->string('kamar', 50)->nullable();
            $table->year('tahun_masuk')->nullable();
            $table->enum('status', ['aktif', 'non_aktif', 'lulus', 'keluar'])->default('aktif');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['nama_lengkap', 'nama_panggilan']);
            $table->index(['kelas', 'kamar']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('santri_profiles');
    }
};