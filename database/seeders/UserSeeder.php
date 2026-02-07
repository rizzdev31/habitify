<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Domain\Enums\UserRole;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdminId = DB::table('users')->insertGetId([
            'name' => 'Super Admin',
            'email' => 'superadmin@habitify.com',
            'password' => Hash::make('password'),
            'role' => UserRole::SUPER_ADMIN->value,
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Guru BK 1
        $bk1Id = DB::table('users')->insertGetId([
            'name' => 'Ustadz Ahmad Fauzi',
            'email' => 'bk1@habitify.com',
            'password' => Hash::make('password'),
            'role' => UserRole::BK->value,
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('bk_profiles')->insert([
            'user_id' => $bk1Id,
            'nip' => 'BK001',
            'nama_lengkap' => 'Ustadz Ahmad Fauzi, S.Psi',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567890',
            'spesialisasi' => 'Konseling Remaja',
            'sertifikasi' => 'Psikolog Klinis',
            'tanggal_bergabung' => '2020-01-01',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Guru BK 2
        $bk2Id = DB::table('users')->insertGetId([
            'name' => 'Ustadzah Siti Aminah',
            'email' => 'bk2@habitify.com',
            'password' => Hash::make('password'),
            'role' => UserRole::BK->value,
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('bk_profiles')->insert([
            'user_id' => $bk2Id,
            'nip' => 'BK002',
            'nama_lengkap' => 'Ustadzah Siti Aminah, S.Pd',
            'jenis_kelamin' => 'P',
            'no_hp' => '081234567891',
            'spesialisasi' => 'Bimbingan Belajar',
            'tanggal_bergabung' => '2021-06-01',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Pengajar 1
        $pengajar1Id = DB::table('users')->insertGetId([
            'name' => 'Ustadz Hasan Abdullah',
            'email' => 'pengajar1@habitify.com',
            'password' => Hash::make('password'),
            'role' => UserRole::PENGAJAR->value,
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pengajar_profiles')->insert([
            'user_id' => $pengajar1Id,
            'nip' => 'PGR001',
            'nama_lengkap' => 'Ustadz Hasan Abdullah, Lc',
            'jenis_kelamin' => 'L',
            'no_hp' => '081234567892',
            'bidang_studi' => 'Fiqih',
            'jabatan' => 'Wali Kelas 2A',
            'tanggal_bergabung' => '2019-07-01',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Pengajar 2
        $pengajar2Id = DB::table('users')->insertGetId([
            'name' => 'Ustadzah Fatimah Zahra',
            'email' => 'pengajar2@habitify.com',
            'password' => Hash::make('password'),
            'role' => UserRole::PENGAJAR->value,
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('pengajar_profiles')->insert([
            'user_id' => $pengajar2Id,
            'nip' => 'PGR002',
            'nama_lengkap' => 'Ustadzah Fatimah Zahra, S.Ag',
            'jenis_kelamin' => 'P',
            'no_hp' => '081234567893',
            'bidang_studi' => 'Bahasa Arab',
            'jabatan' => 'Wali Kelas 1B',
            'tanggal_bergabung' => '2020-07-01',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Users seeded successfully!');
    }
}