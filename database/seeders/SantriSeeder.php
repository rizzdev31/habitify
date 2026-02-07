<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Domain\Enums\UserRole;

class SantriSeeder extends Seeder
{
    public function run(): void
    {
        $santriData = [
            [
                'nama_lengkap' => 'Ahmad Ardi Pratama',
                'nama_panggilan' => 'Ardi',
                'nisn' => '1234567001',
                'jenis_kelamin' => 'L',
                'kelas' => '2A',
                'kamar' => 'Asrama Putra 1',
                'wali_nama' => 'Bapak Pratama',
                'wali_hp' => '081111111001',
            ],
            [
                'nama_lengkap' => 'Budi Santoso',
                'nama_panggilan' => 'Budi',
                'nisn' => '1234567002',
                'jenis_kelamin' => 'L',
                'kelas' => '2A',
                'kamar' => 'Asrama Putra 1',
                'wali_nama' => 'Bapak Santoso',
                'wali_hp' => '081111111002',
            ],
            [
                'nama_lengkap' => 'Cahyo Wibowo',
                'nama_panggilan' => 'Cahyo',
                'nisn' => '1234567003',
                'jenis_kelamin' => 'L',
                'kelas' => '3A',
                'kamar' => 'Asrama Putra 2',
                'wali_nama' => 'Bapak Wibowo',
                'wali_hp' => '081111111003',
            ],
            [
                'nama_lengkap' => 'Dimas Kurniawan',
                'nama_panggilan' => 'Dimas',
                'nisn' => '1234567004',
                'jenis_kelamin' => 'L',
                'kelas' => '2B',
                'kamar' => 'Asrama Putra 1',
                'wali_nama' => 'Bapak Kurniawan',
                'wali_hp' => '081111111004',
            ],
            [
                'nama_lengkap' => 'Eka Fitriani',
                'nama_panggilan' => 'Eka',
                'nisn' => '1234567005',
                'jenis_kelamin' => 'P',
                'kelas' => '2A',
                'kamar' => 'Asrama Putri 1',
                'wali_nama' => 'Ibu Fitriani',
                'wali_hp' => '081111111005',
            ],
            [
                'nama_lengkap' => 'Fatimah Azzahra',
                'nama_panggilan' => 'Fatimah',
                'nisn' => '1234567006',
                'jenis_kelamin' => 'P',
                'kelas' => '1B',
                'kamar' => 'Asrama Putri 1',
                'wali_nama' => 'Bapak Azzahra',
                'wali_hp' => '081111111006',
            ],
            [
                'nama_lengkap' => 'Gilang Ramadhan',
                'nama_panggilan' => 'Gilang',
                'nisn' => '1234567007',
                'jenis_kelamin' => 'L',
                'kelas' => '1A',
                'kamar' => 'Asrama Putra 3',
                'wali_nama' => 'Bapak Ramadhan',
                'wali_hp' => '081111111007',
            ],
            [
                'nama_lengkap' => 'Hana Safitri',
                'nama_panggilan' => 'Hana',
                'nisn' => '1234567008',
                'jenis_kelamin' => 'P',
                'kelas' => '3A',
                'kamar' => 'Asrama Putri 2',
                'wali_nama' => 'Ibu Safitri',
                'wali_hp' => '081111111008',
            ],
            [
                'nama_lengkap' => 'Irfan Maulana',
                'nama_panggilan' => 'Irfan',
                'nisn' => '1234567009',
                'jenis_kelamin' => 'L',
                'kelas' => '2B',
                'kamar' => 'Asrama Putra 2',
                'wali_nama' => 'Bapak Maulana',
                'wali_hp' => '081111111009',
            ],
            [
                'nama_lengkap' => 'Jasmine Putri',
                'nama_panggilan' => 'Jasmine',
                'nisn' => '1234567010',
                'jenis_kelamin' => 'P',
                'kelas' => '1B',
                'kamar' => 'Asrama Putri 1',
                'wali_nama' => 'Ibu Putri',
                'wali_hp' => '081111111010',
            ],
        ];

        foreach ($santriData as $data) {
            // Create Santri User
            $santriUserId = DB::table('users')->insertGetId([
                'name' => $data['nama_lengkap'],
                'email' => strtolower(str_replace(' ', '', $data['nama_panggilan'])) . '@santri.habitify.com',
                'password' => Hash::make('password'),
                'role' => UserRole::SANTRI->value,
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create Santri Profile
            $santriProfileId = DB::table('santri_profiles')->insertGetId([
                'user_id' => $santriUserId,
                'nisn' => $data['nisn'],
                'nama_lengkap' => $data['nama_lengkap'],
                'nama_panggilan' => $data['nama_panggilan'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => fake()->dateTimeBetween('-18 years', '-12 years')->format('Y-m-d'),
                'alamat' => fake()->address(),
                'no_hp' => '08' . fake()->numerify('##########'),
                'no_whatsapp_wali' => $data['wali_hp'],
                'nama_wali' => $data['wali_nama'],
                'kelas' => $data['kelas'],
                'kamar' => $data['kamar'],
                'tahun_masuk' => 2023,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create Santri Points
            DB::table('santri_points')->insert([
                'santri_id' => $santriProfileId,
                'total_poin_pelanggaran' => 0,
                'total_poin_apresiasi' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create Wali User
            $waliUserId = DB::table('users')->insertGetId([
                'name' => $data['wali_nama'],
                'email' => 'wali.' . strtolower(str_replace(' ', '', $data['nama_panggilan'])) . '@habitify.com',
                'password' => Hash::make('password'),
                'role' => UserRole::WALI->value,
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create Wali Profile
            DB::table('wali_profiles')->insert([
                'user_id' => $waliUserId,
                'santri_id' => $santriProfileId,
                'nama_lengkap' => $data['wali_nama'],
                'hubungan' => $data['jenis_kelamin'] === 'L' ? 'Ayah' : 'Ibu',
                'no_hp' => $data['wali_hp'],
                'no_whatsapp' => $data['wali_hp'],
                'pekerjaan' => fake()->jobTitle(),
                'alamat' => fake()->address(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Santri & Wali seeded successfully! (' . count($santriData) . ' records)');
    }
}