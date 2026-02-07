<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KnowledgeBaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPelanggaran();
        $this->seedApresiasi();
        $this->seedKonselor();
        $this->seedKonsekuensi();
        $this->seedReward();
        $this->seedDiagnosis();
        $this->seedDictionary();
        $this->seedRules();
    }

    private function seedPelanggaran(): void
    {
        $data = [
            ['kode' => 'P001', 'nama' => 'Perundungan Fisik', 'poin' => 5, 'konsekuensi' => 'Tanggung Jawab, dan Berdiri Berhadapan selama 30 menit'],
            ['kode' => 'P002', 'nama' => 'Disiplin Waktu', 'poin' => 2, 'konsekuensi' => 'Memberikan Motivasi Belajar'],
            ['kode' => 'P003', 'nama' => 'Vandalisme', 'poin' => 2, 'konsekuensi' => 'Memperbaiki dan Membersihkan'],
            ['kode' => 'P004', 'nama' => 'Kerapian', 'poin' => 2, 'konsekuensi' => 'Merapikan dan Mencontohkan cara yang benar'],
            ['kode' => 'P005', 'nama' => 'Belajar Mengajar', 'poin' => 2, 'konsekuensi' => 'Menyampaikan Motivasi Belajar'],
            ['kode' => 'P006', 'nama' => 'Etika Sikap', 'poin' => 2, 'konsekuensi' => 'Istighfar 50x dan Menyampaikan Motivasi Adab'],
            ['kode' => 'P007', 'nama' => 'Aturan Umum', 'poin' => 2, 'konsekuensi' => 'Istighfar 50x, Menyampaikan Motivasi'],
            ['kode' => 'P008', 'nama' => 'Rokok', 'poin' => 20, 'konsekuensi' => 'Menulis Alquran Juz 30, Menyampaikan Motivasi Selama 1 Minggu'],
            ['kode' => 'P009', 'nama' => 'NAPZA', 'poin' => 200, 'konsekuensi' => 'Dikeluarkan'],
            ['kode' => 'P010', 'nama' => 'Kabur', 'poin' => 10, 'konsekuensi' => 'Menulis Al Quran Surah Al Baqoroh, Menyampaikan Motivasi Selama 2 Minggu'],
            ['kode' => 'P011', 'nama' => 'Pacaran', 'poin' => 20, 'konsekuensi' => 'Menulis Al Quran Surah Al Baqoroh, Menyampaikan Motivasi Selama 2 Minggu'],
            ['kode' => 'P012', 'nama' => 'Senjata Tajam', 'poin' => 10, 'konsekuensi' => 'Menulis Al Quran Juz 29, Menyampaikan Motivasi Selama 1 Minggu'],
            ['kode' => 'P013', 'nama' => 'Kesehatan', 'poin' => 2, 'konsekuensi' => 'Merapikan UKS, Mengontrol Anak Sakit Selama 1 Hari'],
            ['kode' => 'P014', 'nama' => 'Bahasa', 'poin' => 2, 'konsekuensi' => 'Istighfar 50x, Menghafal 5 Mufradat Baru Bahasa Arab'],
            ['kode' => 'P015', 'nama' => 'Perundungan Verbal/Psikis', 'poin' => 3, 'konsekuensi' => 'Istighfar 100x, Meminta Maaf kepada Korban'],
            ['kode' => 'P016', 'nama' => 'Pencurian/Pengambilan Barang', 'poin' => 15, 'konsekuensi' => 'Mengembalikan Barang, Menulis Al Quran Juz 30'],
        ];

        foreach ($data as $item) {
            DB::table('kb_pelanggaran')->updateOrInsert(
                ['kode' => $item['kode']],
                array_merge($item, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedApresiasi(): void
    {
        $data = [
            ['kode' => 'A001', 'nama' => 'Tindakan / Perilaku', 'poin' => 2, 'reward' => 'Apresiasi Ucapan'],
            ['kode' => 'A002', 'nama' => 'Prestasi', 'poin' => 10, 'reward' => 'Apresiasi Ucapan'],
            ['kode' => 'A003', 'nama' => 'Linguistik / Ucapan', 'poin' => 2, 'reward' => 'Apresiasi dan Validasi'],
        ];

        foreach ($data as $item) {
            DB::table('kb_apresiasi')->updateOrInsert(
                ['kode' => $item['kode']],
                array_merge($item, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedKonselor(): void
    {
        $data = [
            ['kode' => 'G001', 'nama' => 'Gangguan Kecemasan Umum'],
            ['kode' => 'G002', 'nama' => 'Gangguan Kecemasan Khusus'],
            ['kode' => 'G003', 'nama' => 'Gangguan Depresi'],
            ['kode' => 'G004', 'nama' => 'Gangguan Stress Akademik'],
            ['kode' => 'G005', 'nama' => 'Gangguan Kelelahan'],
            ['kode' => 'G006', 'nama' => 'Gangguan Konsentrasi dan Fokus'],
            ['kode' => 'G007', 'nama' => 'Gangguan Rendah Diri'],
            ['kode' => 'G008', 'nama' => 'Gangguan Overthinking'],
            ['kode' => 'G009', 'nama' => 'Gangguan Kesepian'],
            ['kode' => 'G010', 'nama' => 'Gangguan Bullying'],
            ['kode' => 'G011', 'nama' => 'Gangguan Kecanduan'],
            ['kode' => 'G012', 'nama' => 'Gangguan Tidur'],
            ['kode' => 'G013', 'nama' => 'Gangguan Demotivasi Belajar'],
            ['kode' => 'G014', 'nama' => 'Gangguan Perfeksionisme'],
            ['kode' => 'G015', 'nama' => 'Gangguan Identitas'],
            ['kode' => 'G016', 'nama' => 'Gangguan Phobia'],
            ['kode' => 'G017', 'nama' => 'Gangguan Emosi'],
            ['kode' => 'G018', 'nama' => 'Gangguan Keluarga'],
            ['kode' => 'G019', 'nama' => 'Gangguan Makan'],
        ];

        foreach ($data as $item) {
            DB::table('kb_konselor')->updateOrInsert(
                ['kode' => $item['kode']],
                array_merge($item, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedKonsekuensi(): void
    {
        $data = [
            ['kode' => 'K001', 'nama' => 'Bimbingan 1', 'threshold_min' => 10, 'threshold_max' => 29, 'tindakan' => 'Bimbingan 1 Dan Pemberian Sanksi Ringan'],
            ['kode' => 'K002', 'nama' => 'Bimbingan 2', 'threshold_min' => 30, 'threshold_max' => 49, 'tindakan' => 'Bimbingan 2 Dan Pemberian Sanksi Ringan'],
            ['kode' => 'K003', 'nama' => 'Bimbingan 3', 'threshold_min' => 50, 'threshold_max' => 69, 'tindakan' => 'Bimbingan 3 Dan Pemberian Sanksi Ringan'],
            ['kode' => 'K004', 'nama' => 'Surat Pernyataan 1', 'threshold_min' => 70, 'threshold_max' => 89, 'tindakan' => 'Surat Pernyataan 1 dan Pemberian Teguran Keras'],
            ['kode' => 'K005', 'nama' => 'Bimbingan 4', 'threshold_min' => 90, 'threshold_max' => 109, 'tindakan' => 'Bimbingan 4 Dan Pemberian Sanksi Sedang'],
            ['kode' => 'K006', 'nama' => 'Surat Pernyataan 2', 'threshold_min' => 110, 'threshold_max' => 129, 'tindakan' => 'Surat Pernyataan 2 dan Pemberian Ancaman Skorsing'],
            ['kode' => 'K007', 'nama' => 'Bimbingan 5', 'threshold_min' => 130, 'threshold_max' => 159, 'tindakan' => 'Bimbingan 5 Dan Pemberian Sanksi Berat'],
            ['kode' => 'K008', 'nama' => 'Bimbingan 6', 'threshold_min' => 160, 'threshold_max' => 189, 'tindakan' => 'Bimbingan 6 Dan Pemberian Sanksi Berat'],
            ['kode' => 'K009', 'nama' => 'Skorsing', 'threshold_min' => 190, 'threshold_max' => 199, 'tindakan' => 'Skorsing'],
            ['kode' => 'K010', 'nama' => 'Drop Out', 'threshold_min' => 200, 'threshold_max' => null, 'tindakan' => 'Surat Pernyataan 3 Dan Drop Out'],
        ];

        foreach ($data as $item) {
            DB::table('kb_konsekuensi')->updateOrInsert(
                ['kode' => $item['kode']],
                array_merge($item, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedReward(): void
    {
        $data = [
            ['kode' => 'R001', 'nama' => 'Reward Level 1', 'threshold_min' => 30, 'threshold_max' => 59, 'reward' => 'Pemberian Voucher An Nur Corner 5.000 + Apresiasi'],
            ['kode' => 'R002', 'nama' => 'Reward Level 2', 'threshold_min' => 60, 'threshold_max' => 89, 'reward' => 'Pemberian Voucher An Nur Corner 5.000 + Apresiasi'],
            ['kode' => 'R003', 'nama' => 'Reward Level 3', 'threshold_min' => 90, 'threshold_max' => 129, 'reward' => 'Pemberian Voucher An Nur Cafe 10.000 + Apresiasi'],
            ['kode' => 'R004', 'nama' => 'Reward Level 4', 'threshold_min' => 130, 'threshold_max' => 149, 'reward' => 'Pemberian Piagam Penghargaan + Voucher An Nur Cafe 10.000 + Apresiasi'],
            ['kode' => 'R005', 'nama' => 'Reward Level 5', 'threshold_min' => 150, 'threshold_max' => null, 'reward' => 'Pemberian Piagam Penghargaan + Voucher An Nur Cafe 20.000 + Apresiasi'],
        ];

        foreach ($data as $item) {
            DB::table('kb_reward')->updateOrInsert(
                ['kode' => $item['kode']],
                array_merge($item, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedDiagnosis(): void
    {
        $data = [
            // Kategori A - Korban
            ['kode' => 'DX-A01', 'nama' => 'PTSD Akut (Fisik)', 'kategori' => 'korban', 'severity' => 'high', 'penjelasan' => 'Santri mengalami trauma akut akibat kekerasan fisik', 'rekomendasi' => 'Segera lakukan Psychological First Aid (PFA) untuk menstabilkan emosi. Ajarkan teknik grounding. Pastikan santri merasa aman. Jika gejala berlanjut >1 bulan, rujuk ke psikolog.'],
            ['kode' => 'DX-A02', 'nama' => 'Social Withdrawal', 'kategori' => 'korban', 'severity' => 'medium', 'penjelasan' => 'Santri menarik diri dari pergaulan sosial', 'rekomendasi' => 'Lakukan konseling individu. Libatkan dalam kelompok kecil dengan Peer Support. Berikan afirmasi positif secara rutin.'],
            ['kode' => 'DX-A03', 'nama' => 'Flight Response', 'kategori' => 'korban', 'severity' => 'high', 'penjelasan' => 'Santri memiliki keinginan untuk kabur', 'rekomendasi' => 'Validasi perasaan takutnya. Hubungi wali untuk mediasi. Evaluasi keamanan lingkungan.'],
            ['kode' => 'DX-A04', 'nama' => 'School Refusal', 'kategori' => 'korban', 'severity' => 'medium', 'penjelasan' => 'Santri menolak untuk bersekolah', 'rekomendasi' => 'Cek kesehatan fisik. Identifikasi pemicu ketakutan. Buat target kehadiran bertahap.'],
            ['kode' => 'DX-A05', 'nama' => 'Cognitive Decline', 'kategori' => 'korban', 'severity' => 'medium', 'penjelasan' => 'Penurunan fungsi kognitif akibat tekanan', 'rekomendasi' => 'Berikan pendampingan remedial. Kurangi beban tugas sementara. Fokus pada pemulihan konsentrasi.'],
            ['kode' => 'DX-A06', 'nama' => 'Paranoid Defense', 'kategori' => 'korban', 'severity' => 'critical', 'penjelasan' => 'Santri membawa benda berbahaya untuk perlindungan', 'rekomendasi' => 'Amankan benda berbahaya dengan persuasif. Gali sumber ancaman. Berikan jaminan keamanan.'],
            ['kode' => 'DX-A07', 'nama' => 'Depressive Eating', 'kategori' => 'korban', 'severity' => 'medium', 'penjelasan' => 'Gangguan makan akibat depresi', 'rekomendasi' => 'Monitor berat badan dan pola makan. Dampingi saat jam makan. Edukasi hubungan emosi dan makan.'],
            ['kode' => 'DX-A08', 'nama' => 'Trauma Dependency', 'kategori' => 'korban', 'severity' => 'medium', 'penjelasan' => 'Ketergantungan tidak sehat akibat trauma', 'rekomendasi' => 'Bantu mengenali ketergantungan tidak sehat. Latih kemandirian. Batasi akses ke figur ketergantungan.'],
            ['kode' => 'DX-A09', 'nama' => 'Reaktif Agresif', 'kategori' => 'korban', 'severity' => 'high', 'penjelasan' => 'Reaksi agresif sebagai mekanisme pertahanan', 'rekomendasi' => 'Ajarkan penyaluran amarah sehat. Berikan pemahaman konsekuensi. Lakukan mediasi.'],
            ['kode' => 'DX-A10', 'nama' => 'Numbing (Zat)', 'kategori' => 'korban', 'severity' => 'critical', 'penjelasan' => 'Penggunaan zat untuk mati rasa dari trauma', 'rekomendasi' => 'Isolasi dari akses zat. Panggil orang tua dan tes urine. Rujuk ke rehabilitasi.'],
            ['kode' => 'DX-A11', 'nama' => 'Learned Helplessness', 'kategori' => 'korban', 'severity' => 'medium', 'penjelasan' => 'Ketidakberdayaan yang dipelajari', 'rekomendasi' => 'Berikan tugas kecil yang mudah. Berikan pujian pada keberhasilan. Jadwalkan sesi kebersihan diri.'],
            ['kode' => 'DX-A12', 'nama' => 'Identity Crisis', 'kategori' => 'korban', 'severity' => 'medium', 'penjelasan' => 'Krisis identitas akibat pengalaman traumatis', 'rekomendasi' => 'Eksplorasi minat dan bakat. Hindari pelabelan negatif. Libatkan dalam ekstrakurikuler positif.'],

            // Kategori B - Pelaku
            ['kode' => 'DX-B01', 'nama' => 'IED (Ledakan Amarah)', 'kategori' => 'pelaku', 'severity' => 'high', 'penjelasan' => 'Gangguan pengendalian amarah impulsif', 'rekomendasi' => 'Ajarkan teknik Stop-Think-Act. Bantu identifikasi trigger amarah. Berikan konsekuensi logis dan mendidik.'],
            ['kode' => 'DX-B02', 'nama' => 'Compensatory Bullying', 'kategori' => 'pelaku', 'severity' => 'high', 'penjelasan' => 'Bullying sebagai kompensasi rasa tidak aman', 'rekomendasi' => 'Gali perasaan insecure atau masalah rumah. Berikan tanggung jawab kepemimpinan positif. Wajibkan minta maaf tulus.'],
            ['kode' => 'DX-B03', 'nama' => 'Displaced Aggression', 'kategori' => 'pelaku', 'severity' => 'high', 'penjelasan' => 'Agresi yang dipindahkan dari sumber asli', 'rekomendasi' => 'Lakukan konseling keluarga. Sadarkan bahwa teman bukan sasaran pelampiasan. Latih empati.'],
            ['kode' => 'DX-B04', 'nama' => 'Substance Violence', 'kategori' => 'pelaku', 'severity' => 'critical', 'penjelasan' => 'Kekerasan di bawah pengaruh zat', 'rekomendasi' => 'Lakukan penanganan medis detoksifikasi. Terapkan tindakan disipliner tegas. Wajibkan lapor diri dan konseling rutin.'],
            ['kode' => 'DX-B05', 'nama' => 'Sensation Seeking', 'kategori' => 'pelaku', 'severity' => 'medium', 'penjelasan' => 'Perilaku mencari sensasi berbahaya', 'rekomendasi' => 'Salurkan energi ke kegiatan fisik intensitas tinggi. Berikan tantangan kepanitiaan. Kurangi waktu luang tidak terstruktur.'],
            ['kode' => 'DX-B06', 'nama' => 'Bully-Victim Cycle', 'kategori' => 'pelaku', 'severity' => 'high', 'penjelasan' => 'Pelaku yang pernah menjadi korban', 'rekomendasi' => 'Tangani trauma masa lalu. Putus rantai balas dendam dengan mediasi restoratif. Bangun kesadaran.'],
            ['kode' => 'DX-B07', 'nama' => 'Peer Pressure Aggression', 'kategori' => 'pelaku', 'severity' => 'medium', 'penjelasan' => 'Agresi akibat tekanan kelompok', 'rekomendasi' => 'Pisahkan dari geng negatif sementara. Latih Assertiveness. Dekatkan dengan kelompok berprestasi.'],
            ['kode' => 'DX-B08', 'nama' => 'Instrumental Aggression', 'kategori' => 'pelaku', 'severity' => 'high', 'penjelasan' => 'Agresi untuk mendapatkan sesuatu', 'rekomendasi' => 'Terapkan sanksi restitusi. Edukasi nilai kejujuran dan konsekuensi hukum. Pantau penggunaan uang saku.'],
            ['kode' => 'DX-B09', 'nama' => 'Verbal Impulsivity', 'kategori' => 'pelaku', 'severity' => 'low', 'penjelasan' => 'Impulsivitas verbal tanpa kontrol', 'rekomendasi' => 'Terapkan sistem denda kosa kata atau istighfar. Latih Puasa Bicara atau teknik 5 detik. Berikan contoh komunikasi santun.'],
            ['kode' => 'DX-B10', 'nama' => 'Conduct Disorder', 'kategori' => 'pelaku', 'severity' => 'critical', 'penjelasan' => 'Gangguan perilaku serius dan berulang', 'rekomendasi' => 'Buat kontrak perilaku ketat dengan target harian. Kolaborasi Wali Kamar, BK, Orang Tua. Evaluasi kelayakan tinggal di asrama.'],
            ['kode' => 'DX-B11', 'nama' => 'Gang Violence Risk', 'kategori' => 'pelaku', 'severity' => 'critical', 'penjelasan' => 'Risiko kekerasan terkait geng', 'rekomendasi' => 'Identifikasi jaringan geng dan laporkan kesiswaan. Razia rutin barang bawaan. Program deradikalisasi geng.'],
            ['kode' => 'DX-B12', 'nama' => 'Authority Conflict', 'kategori' => 'pelaku', 'severity' => 'medium', 'penjelasan' => 'Konflik dengan otoritas', 'rekomendasi' => 'Hindari adu argumen frontal, gunakan diskusi logis. Berikan ruang otonomi terbatas. Jelaskan alasan rasional aturan.'],

            // Kategori C - Internal
            ['kode' => 'DX-C01', 'nama' => 'ADHD Inattentive', 'kategori' => 'internal', 'severity' => 'medium', 'penjelasan' => 'Gangguan pemusatan perhatian', 'rekomendasi' => 'Minta duduk di barisan depan. Berikan instruksi satu per satu. Sarankan metode belajar visual dan kinestetik.'],
            ['kode' => 'DX-C02', 'nama' => 'Academic Burnout', 'kategori' => 'internal', 'severity' => 'medium', 'penjelasan' => 'Kelelahan akademik', 'rekomendasi' => 'Ajarkan manajemen waktu dan prioritas. Dorong ambil jeda istirahat. Evaluasi target nilai agar realistis.'],
            ['kode' => 'DX-C03', 'nama' => 'Addiction Motivation', 'kategori' => 'internal', 'severity' => 'high', 'penjelasan' => 'Kecanduan yang mempengaruhi motivasi', 'rekomendasi' => 'Detoksifikasi gadget/game total. Ganti sumber dopamin dengan olahraga/hobi. Buat jadwal harian ketat.'],
            ['kode' => 'DX-C04', 'nama' => 'Academic Helplessness', 'kategori' => 'internal', 'severity' => 'medium', 'penjelasan' => 'Ketidakberdayaan dalam akademik', 'rekomendasi' => 'Identifikasi gaya belajar yang cocok. Berikan tutor sebaya yang sabar. Fokuskan apresiasi pada usaha bukan hasil.'],
            ['kode' => 'DX-C05', 'nama' => 'Analysis Paralysis', 'kategori' => 'internal', 'severity' => 'low', 'penjelasan' => 'Kelumpuhan akibat terlalu banyak analisis', 'rekomendasi' => 'Pecah tugas besar menjadi langkah kecil. Tetapkan deadline pendek per langkah. Kurangi opsi pilihan.'],
            ['kode' => 'DX-C06', 'nama' => 'Depresi Mayor Fisik', 'kategori' => 'internal', 'severity' => 'critical', 'penjelasan' => 'Depresi berat dengan gejala fisik', 'rekomendasi' => 'Pastikan kebutuhan dasar terpenuhi. Dampingi setiap aktivitas rutin. Rujuk ke Psikiater untuk evaluasi medis.'],
            ['kode' => 'DX-C07', 'nama' => 'Generalized Anxiety', 'kategori' => 'internal', 'severity' => 'medium', 'penjelasan' => 'Kecemasan umum berlebihan', 'rekomendasi' => 'Latih teknik relaksasi otot progresif. Kurangi konsumsi kafein. Minta buat jurnal kekhawatiran.'],
            ['kode' => 'DX-C08', 'nama' => 'Isolation Flight', 'kategori' => 'internal', 'severity' => 'medium', 'penjelasan' => 'Melarikan diri ke isolasi', 'rekomendasi' => 'Cek lokasi persembunyian favoritnya. Ajak bicara santai (building rapport). Cari buddy sebagai pendamping.'],
            ['kode' => 'DX-C09', 'nama' => 'NSSI Risk - Self Harm', 'kategori' => 'internal', 'severity' => 'critical', 'penjelasan' => 'Risiko menyakiti diri sendiri', 'rekomendasi' => 'Cek fisik ada luka, segera obati. Amankan benda tajam. Berikan coping pengganti aman. Wajib rujuk ke Psikolog.'],
            ['kode' => 'DX-C10', 'nama' => 'Mood Dysregulation', 'kategori' => 'internal', 'severity' => 'medium', 'penjelasan' => 'Disregulasi suasana hati', 'rekomendasi' => 'Minta catat Mood Tracker harian. Bantu kenali tanda fase naik/turun. Edukasi regulasi emosi dasar.'],
            ['kode' => 'DX-C11', 'nama' => 'Nicotine Dependence', 'kategori' => 'internal', 'severity' => 'medium', 'penjelasan' => 'Ketergantungan nikotin', 'rekomendasi' => 'Edukasi bahaya kesehatan dengan visual. Berikan permen karet sebagai pengganti. Kurangi akses uang saku.'],
            ['kode' => 'DX-C12', 'nama' => 'Addiction Criminality', 'kategori' => 'internal', 'severity' => 'high', 'penjelasan' => 'Kecanduan yang mengarah ke kriminalitas', 'rekomendasi' => 'Tangani kecanduan sebagai akar masalah. Terapkan konsekuensi pengembalian barang. Pengawasan ketat 24 jam.'],
            ['kode' => 'DX-C13', 'nama' => 'Gaming Disorder', 'kategori' => 'internal', 'severity' => 'medium', 'penjelasan' => 'Gangguan kecanduan game', 'rekomendasi' => 'Batasi akses wifi dan internet. Wajibkan kegiatan fisik sore hari. Berikan CBT dasar.'],
            ['kode' => 'DX-C14', 'nama' => 'High Risk Sexual', 'kategori' => 'internal', 'severity' => 'high', 'penjelasan' => 'Perilaku seksual berisiko tinggi', 'rekomendasi' => 'Edukasi kesehatan reproduksi dan tinjauan agama. Batasi interaksi berisiko. Konseling penghargaan tubuh dan masa depan.'],
            ['kode' => 'DX-C15', 'nama' => 'Maladaptive Coping', 'kategori' => 'internal', 'severity' => 'medium', 'penjelasan' => 'Mekanisme koping yang tidak sehat', 'rekomendasi' => 'Identifikasi sumber stres utama. Ajarkan coping sehat (curhat, doa, olahraga). Hentikan perilaku koping buruk.'],
            ['kode' => 'DX-C16', 'nama' => 'Broken Home Escape', 'kategori' => 'internal', 'severity' => 'medium', 'penjelasan' => 'Melarikan diri dari masalah keluarga', 'rekomendasi' => 'Jadi pendengar aktif. Jangan paksa mencintai orang tua saat konflik. Fokuskan asrama sebagai Rumah Kedua.'],
            ['kode' => 'DX-C17', 'nama' => 'Family Stress Impact', 'kategori' => 'internal', 'severity' => 'medium', 'penjelasan' => 'Dampak stress keluarga', 'rekomendasi' => 'Komunikasikan dengan orang tua tentang dampak konflik. Berikan dispensasi waktu tugas. Validasi perasaan anak.'],
            ['kode' => 'DX-C18', 'nama' => 'Behavioral Modeling', 'kategori' => 'internal', 'severity' => 'low', 'penjelasan' => 'Meniru perilaku negatif dari lingkungan', 'rekomendasi' => 'Tunjukkan Role Model positif di asrama. Koreksi dengan penjelasan logis. Beri apresiasi saat meniru perilaku baik.'],
            ['kode' => 'DX-C19', 'nama' => 'Home Attachment', 'kategori' => 'internal', 'severity' => 'low', 'penjelasan' => 'Keterikatan berlebihan pada rumah', 'rekomendasi' => 'Batasi frekuensi telepon rumah sementara. Sibukkan dengan kegiatan pondok menyenangkan. Arahkan fokus ke kegiatan masa kini.'],
        ];

        foreach ($data as $item) {
            DB::table('kb_diagnosis')->updateOrInsert(
                ['kode' => $item['kode']],
                array_merge($item, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedDictionary(): void
    {
        DB::table('kb_dictionary')->truncate();

        $dictionary = [
            'pelanggaran' => [
                'P001' => ['pukul', 'tendang', 'tonjok', 'hantam', 'tampar', 'jambak', 'cekik', 'lukai', 'keroyok', 'kelahi', 'fisik', 'serang', 'keras'],
                'P002' => ['terlambat', 'telat', 'siang', 'ngaret', 'molor', 'bolos', 'apel'],
                'P003' => ['rusak', 'pecah', 'coret', 'hancur', 'banting', 'robek', 'kotor', 'vandal'],
                'P004' => ['gondrong', 'keluar', 'ketat', 'atribut', 'seragam', 'kuku', 'rapi', 'berantakan'],
                'P005' => ['tidur', 'kantuk', 'gaduh', 'berisik', 'main', 'pelajaran', 'pr', 'bolos'],
                'P006' => ['bantah', 'lawan', 'nyolot', 'muka', 'salam', 'sopan', 'acuh'],
                'P007' => ['serobot', 'antri', 'sampah', 'sembarangan', 'langgar', 'aturan', 'izin', 'terobos'],
                'P008' => ['rokok', 'hisap', 'vape', 'bakar', 'asap', 'korek', 'nyebat'],
                'P009' => ['miras', 'mabuk', 'alkohol', 'teler', 'obat', 'larang', 'pil', 'koplo', 'narkoba', 'napza', 'ganja', 'sabu'],
                'P010' => ['kabur', 'loncat', 'pagar', 'minggat', 'pulang', 'lari', 'hilang', 'asrama'],
                'P011' => ['pacar', 'cinta', 'berdua', 'mojok', 'pegang', 'tangan', 'khalwat', 'ketemu', 'mesra'],
                'P012' => ['senjata', 'pisau', 'clurit', 'cutter', 'silet', 'tajam', 'bahaya', 'tusuk', 'bacok'],
                'P013' => ['jorok', 'kasur', 'kotor', 'lemari', 'bau', 'piket', 'pura', 'sakit', 'bohong'],
                'P014' => ['kasar', 'anjing', 'babi', 'teriak', 'maki', 'umpat', 'jorok'],
                'P015' => ['ejek', 'hina', 'caci', 'maki', 'bully', 'olok', 'ledek', 'rendah', 'sindir', 'gosip', 'fitnah'],
                'P016' => ['curi', 'ambil', 'comot', 'copet', 'gasak', 'embat', 'sikat', 'gondol', 'tilep'],
            ],
            'apresiasi' => [
                'A001' => ['bantu', 'tolong', 'inisiatif', 'papah', 'tuntun', 'angkat', 'gotong', 'royong', 'sedekah', 'infaq', 'rawat', 'shalat', 'puasa', 'jamaah', 'masjid', 'wudhu', 'tahajud', 'dhuha', 'piket', 'bersih', 'rapi', 'sapu', 'pel', 'tertib', 'antri', 'seragam', 'patuh', 'taat', 'hadir', 'catat', 'simak', 'dengar', 'disiplin', 'tepat', 'kerja', 'senyum', 'hormat', 'jabat', 'salam', 'sopan'],
                'A002' => ['juara', 'menang', 'lomba', 'kompetisi', 'piala', 'medali', 'sertifikat', 'piagam', 'harga', 'wakil', 'olimpiade', 'tanding', 'unggul', 'nilai', 'tuntas', 'lulus', 'rangking', 'peringkat', 'sempurna', 'baik', 'teladan', 'mahir', 'karya', 'cipta', 'inovasi', 'hafal', 'setor', 'lancar'],
                'A003' => ['maaf', 'permisi', 'terima', 'kasih', 'sapa', 'lembut', 'santun', 'halus', 'izin', 'jawab', 'jujur', 'benar', 'amanah', 'janji', 'adzan', 'iqamah', 'tilawah', 'ngaji', 'doa', 'dzikir', 'shalawat', 'istighfar', 'takbir', 'amin', 'hibur', 'nasehat', 'damai', 'ajak', 'pimpin', 'presentasi', 'diskusi', 'tanya', 'usul', 'saran', 'lapor'],
            ],
            'konselor' => [
                'G001' => ['cemas', 'gelisah', 'gugup', 'khawatir', 'waswas', 'takut', 'tegang', 'panik', 'keringat', 'gemetar', 'degdeg', 'jantung', 'debar', 'resah'],
                'G002' => ['fobia', 'hindar', 'situasi', 'gigil', 'pucat', 'histeris', 'jerit', 'lemas'],
                'G003' => ['sedih', 'murung', 'nangis', 'tangis', 'diam', 'pendiam', 'tutup', 'putus', 'asa', 'hampa', 'kosong', 'duka', 'kecewa', 'suram'],
                'G004' => ['pusing', 'sakit', 'kepala', 'tugas', 'nilai', 'turun', 'anjlok', 'susah', 'sulit', 'paham', 'beban', 'tekan', 'tuntut', 'ujian', 'gagal'],
                'G005' => ['lelah', 'capek', 'letih', 'lesu', 'lunglai', 'lemas', 'kantuk', 'energi', 'habis', 'stamina', 'loyo'],
                'G006' => ['lamun', 'bengong', 'tatap', 'kosong', 'lupa', 'pikun', 'fokus', 'pecah', 'bingung', 'lambat', 'respon'],
                'G007' => ['malu', 'minder', 'bodoh', 'jelek', 'mampu', 'ragu', 'tunduk', 'sembunyi', 'insecure', 'banding'],
                'G008' => ['pikir', 'terus', 'bayang', 'takut', 'salah', 'analisis', 'lebih', 'rumit', 'jelimet', 'depan'],
                'G009' => ['sendiri', 'asing', 'jauh', 'teman', 'pisah', 'sepi', 'sunyi', 'isolasi', 'kucil', 'jauhi'],
                'G010' => ['korban', 'bully', 'rundung', 'ejek', 'hina', 'sakiti', 'ancam', 'palak'],
                'G011' => ['candu', 'ketagih', 'terus', 'henti', 'main', 'game', 'hp', 'gadget', 'ponsel', 'rokok', 'gantung', 'lepas'],
                'G012' => ['tidur', 'sulit', 'susah', 'gadang', 'begadang', 'melek', 'terjaga', 'bangun', 'siang', 'lelap', 'mimpi', 'buruk', 'igau'],
                'G013' => ['malas', 'bosan', 'jenuh', 'enggan', 'gairah', 'minat', 'hilang', 'sekolah', 'belajar', 'apatis', 'bodoh'],
                'G014' => ['sempurna', 'perfect', 'salah', 'ulang', 'rapi', 'detail', 'rinci', 'puas', 'kritik', 'standar', 'tinggi'],
                'G015' => ['bingung', 'tujuan', 'arah', 'ubah', 'labil', 'bimbang', 'ikut', 'gaya', 'tren', 'tiru', 'jati', 'diri'],
                'G016' => ['takut', 'benda', 'hewan', 'tempat', 'tinggi', 'gelap', 'sempit', 'darah', 'jarum', 'suntik', 'pingsan'],
                'G017' => ['marah', 'emosi', 'ledak', 'teriak', 'banting', 'rusak', 'sensi', 'sensitif', 'singgung', 'mood', 'kesal', 'benci', 'dendam'],
                'G018' => ['rumah', 'orangtua', 'ayah', 'ibu', 'cerai', 'tengkar', 'pisah', 'konflik', 'kangen', 'rindu', 'pulang', 'kirim', 'uang'],
                'G019' => ['makan', 'nafsu', 'selera', 'kurus', 'gemuk', 'muntah', 'lapar', 'kenyang', 'tolak', 'diet', 'berat', 'badan'],
            ],
        ];

        $records = [];
        foreach ($dictionary as $tipe => $codes) {
            foreach ($codes as $kode => $words) {
                foreach ($words as $word) {
                    $records[] = [
                        'kata' => $word,
                        'kode_referensi' => $kode,
                        'tipe' => $tipe,
                        'bobot' => 1,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }
        DB::table('kb_dictionary')->insert($records);
    }

    private function seedRules(): void
    {
        $rules = [
            // Kategori A - Korban
            ['kode' => 'RA-01', 'nama' => 'PTSD Akut Fisik', 'kategori' => 'korban', 'conditions' => ['G010', 'P001', 'G001'], 'diagnosis_kode' => 'DX-A01', 'prioritas' => 1],
            ['kode' => 'RA-02', 'nama' => 'Social Withdrawal', 'kategori' => 'korban', 'conditions' => ['G010', 'P015', 'G009'], 'diagnosis_kode' => 'DX-A02', 'prioritas' => 2],
            ['kode' => 'RA-03', 'nama' => 'Flight Response', 'kategori' => 'korban', 'conditions' => ['G010', 'P001', 'P010'], 'diagnosis_kode' => 'DX-A03', 'prioritas' => 1],
            ['kode' => 'RA-04', 'nama' => 'School Refusal', 'kategori' => 'korban', 'conditions' => ['G010', 'P002', 'G001'], 'diagnosis_kode' => 'DX-A04', 'prioritas' => 3],
            ['kode' => 'RA-05', 'nama' => 'Cognitive Decline', 'kategori' => 'korban', 'conditions' => ['G010', 'G004', 'G006'], 'diagnosis_kode' => 'DX-A05', 'prioritas' => 3],
            ['kode' => 'RA-06', 'nama' => 'Paranoid Defense', 'kategori' => 'korban', 'conditions' => ['G010', 'P012', 'G001'], 'diagnosis_kode' => 'DX-A06', 'prioritas' => 1],
            ['kode' => 'RA-07', 'nama' => 'Depressive Eating', 'kategori' => 'korban', 'conditions' => ['G010', 'G003', 'G019'], 'diagnosis_kode' => 'DX-A07', 'prioritas' => 2],
            ['kode' => 'RA-08', 'nama' => 'Trauma Dependency', 'kategori' => 'korban', 'conditions' => ['G010', 'P011', 'G007'], 'diagnosis_kode' => 'DX-A08', 'prioritas' => 3],
            ['kode' => 'RA-09', 'nama' => 'Reaktif Agresif', 'kategori' => 'korban', 'conditions' => ['G010', 'G017', 'P003'], 'diagnosis_kode' => 'DX-A09', 'prioritas' => 2],
            ['kode' => 'RA-10', 'nama' => 'Numbing Zat', 'kategori' => 'korban', 'conditions' => ['G010', 'P009', 'G003'], 'diagnosis_kode' => 'DX-A10', 'prioritas' => 1],
            ['kode' => 'RA-11', 'nama' => 'Learned Helplessness', 'kategori' => 'korban', 'conditions' => ['G010', 'G007', 'G005'], 'diagnosis_kode' => 'DX-A11', 'prioritas' => 3],
            ['kode' => 'RA-12', 'nama' => 'Identity Crisis Korban', 'kategori' => 'korban', 'conditions' => ['G010', 'G015', 'G007'], 'diagnosis_kode' => 'DX-A12', 'prioritas' => 3],
            // Kategori B - Pelaku
            ['kode' => 'RB-01', 'nama' => 'IED Ledakan Amarah', 'kategori' => 'pelaku', 'conditions' => ['P001', 'G017', 'P006'], 'diagnosis_kode' => 'DX-B01', 'prioritas' => 1],
            ['kode' => 'RB-02', 'nama' => 'Compensatory Bullying', 'kategori' => 'pelaku', 'conditions' => ['P001', 'P015', 'G007'], 'diagnosis_kode' => 'DX-B02', 'prioritas' => 2],
            ['kode' => 'RB-03', 'nama' => 'Displaced Aggression', 'kategori' => 'pelaku', 'conditions' => ['P001', 'G018', 'G017'], 'diagnosis_kode' => 'DX-B03', 'prioritas' => 2],
            ['kode' => 'RB-04', 'nama' => 'Substance Violence', 'kategori' => 'pelaku', 'conditions' => ['P001', 'P009', 'G017'], 'diagnosis_kode' => 'DX-B04', 'prioritas' => 1],
            ['kode' => 'RB-05', 'nama' => 'Sensation Seeking', 'kategori' => 'pelaku', 'conditions' => ['P001', 'P007', 'P010'], 'diagnosis_kode' => 'DX-B05', 'prioritas' => 3],
            ['kode' => 'RB-06', 'nama' => 'Bully Victim Cycle', 'kategori' => 'pelaku', 'conditions' => ['P001', 'G010', 'P015'], 'diagnosis_kode' => 'DX-B06', 'prioritas' => 2],
            ['kode' => 'RB-07', 'nama' => 'Peer Pressure Aggression', 'kategori' => 'pelaku', 'conditions' => ['P001', 'P015', 'G009'], 'diagnosis_kode' => 'DX-B07', 'prioritas' => 3],
            ['kode' => 'RB-08', 'nama' => 'Instrumental Aggression', 'kategori' => 'pelaku', 'conditions' => ['P001', 'P016', 'P006'], 'diagnosis_kode' => 'DX-B08', 'prioritas' => 2],
            ['kode' => 'RB-09', 'nama' => 'Verbal Impulsivity', 'kategori' => 'pelaku', 'conditions' => ['P014', 'G017', 'P006'], 'diagnosis_kode' => 'DX-B09', 'prioritas' => 4],
            ['kode' => 'RB-10', 'nama' => 'Conduct Disorder', 'kategori' => 'pelaku', 'conditions' => ['P001', 'P003', 'P006', 'P007'], 'diagnosis_kode' => 'DX-B10', 'prioritas' => 1],
            ['kode' => 'RB-11', 'nama' => 'Gang Violence Risk', 'kategori' => 'pelaku', 'conditions' => ['P001', 'P012', 'P010'], 'diagnosis_kode' => 'DX-B11', 'prioritas' => 1],
            ['kode' => 'RB-12', 'nama' => 'Authority Conflict', 'kategori' => 'pelaku', 'conditions' => ['P006', 'G017', 'P007'], 'diagnosis_kode' => 'DX-B12', 'prioritas' => 3],
            // Kategori C - Internal
            ['kode' => 'RC-01', 'nama' => 'ADHD Inattentive', 'kategori' => 'internal', 'conditions' => ['P005', 'G006', 'G004'], 'diagnosis_kode' => 'DX-C01', 'prioritas' => 3],
            ['kode' => 'RC-02', 'nama' => 'Academic Burnout', 'kategori' => 'internal', 'conditions' => ['G004', 'G005', 'G013'], 'diagnosis_kode' => 'DX-C02', 'prioritas' => 3],
            ['kode' => 'RC-03', 'nama' => 'Addiction Motivation', 'kategori' => 'internal', 'conditions' => ['P002', 'G013', 'G011'], 'diagnosis_kode' => 'DX-C03', 'prioritas' => 2],
            ['kode' => 'RC-04', 'nama' => 'Academic Helplessness', 'kategori' => 'internal', 'conditions' => ['G004', 'G007', 'G013'], 'diagnosis_kode' => 'DX-C04', 'prioritas' => 3],
            ['kode' => 'RC-05', 'nama' => 'Analysis Paralysis', 'kategori' => 'internal', 'conditions' => ['G008', 'G014', 'G004'], 'diagnosis_kode' => 'DX-C05', 'prioritas' => 4],
            ['kode' => 'RC-06', 'nama' => 'Depresi Mayor Fisik', 'kategori' => 'internal', 'conditions' => ['G003', 'G019', 'G005'], 'diagnosis_kode' => 'DX-C06', 'prioritas' => 1],
            ['kode' => 'RC-07', 'nama' => 'Generalized Anxiety', 'kategori' => 'internal', 'conditions' => ['G001', 'G008', 'G012'], 'diagnosis_kode' => 'DX-C07', 'prioritas' => 2],
            ['kode' => 'RC-08', 'nama' => 'Isolation Flight', 'kategori' => 'internal', 'conditions' => ['G009', 'P010', 'G003'], 'diagnosis_kode' => 'DX-C08', 'prioritas' => 2],
            ['kode' => 'RC-09', 'nama' => 'NSSI Risk Self Harm', 'kategori' => 'internal', 'conditions' => ['G003', 'G017', 'G007'], 'diagnosis_kode' => 'DX-C09', 'prioritas' => 1],
            ['kode' => 'RC-10', 'nama' => 'Mood Dysregulation', 'kategori' => 'internal', 'conditions' => ['G017', 'G003', 'G012'], 'diagnosis_kode' => 'DX-C10', 'prioritas' => 2],
            ['kode' => 'RC-11', 'nama' => 'Nicotine Dependence', 'kategori' => 'internal', 'conditions' => ['P008', 'G011', 'G001'], 'diagnosis_kode' => 'DX-C11', 'prioritas' => 3],
            ['kode' => 'RC-12', 'nama' => 'Addiction Criminality', 'kategori' => 'internal', 'conditions' => ['G011', 'P016', 'P008'], 'diagnosis_kode' => 'DX-C12', 'prioritas' => 2],
            ['kode' => 'RC-13', 'nama' => 'Gaming Disorder', 'kategori' => 'internal', 'conditions' => ['G011', 'G012', 'G013'], 'diagnosis_kode' => 'DX-C13', 'prioritas' => 3],
            ['kode' => 'RC-14', 'nama' => 'High Risk Sexual', 'kategori' => 'internal', 'conditions' => ['P011', 'G011', 'P007'], 'diagnosis_kode' => 'DX-C14', 'prioritas' => 2],
            ['kode' => 'RC-15', 'nama' => 'Maladaptive Coping', 'kategori' => 'internal', 'conditions' => ['G001', 'P008', 'G012'], 'diagnosis_kode' => 'DX-C15', 'prioritas' => 3],
            ['kode' => 'RC-16', 'nama' => 'Broken Home Escape', 'kategori' => 'internal', 'conditions' => ['G018', 'P010', 'G003'], 'diagnosis_kode' => 'DX-C16', 'prioritas' => 2],
            ['kode' => 'RC-17', 'nama' => 'Family Stress Impact', 'kategori' => 'internal', 'conditions' => ['G018', 'G004', 'G017'], 'diagnosis_kode' => 'DX-C17', 'prioritas' => 3],
            ['kode' => 'RC-18', 'nama' => 'Behavioral Modeling', 'kategori' => 'internal', 'conditions' => ['P006', 'G015', 'P014'], 'diagnosis_kode' => 'DX-C18', 'prioritas' => 4],
            ['kode' => 'RC-19', 'nama' => 'Home Attachment', 'kategori' => 'internal', 'conditions' => ['G018', 'G009', 'G003'], 'diagnosis_kode' => 'DX-C19', 'prioritas' => 4],
        ];

        foreach ($rules as $rule) {
            DB::table('kb_rules')->updateOrInsert(
                ['kode' => $rule['kode']],
                [
                    'nama' => $rule['nama'],
                    'kategori' => $rule['kategori'],
                    'conditions' => json_encode($rule['conditions']),
                    'operator' => 'AND',
                    'min_match' => 0,
                    'diagnosis_kode' => $rule['diagnosis_kode'],
                    'prioritas' => $rule['prioritas'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}