<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Expert System Configuration
    |--------------------------------------------------------------------------
    */
    'expert_system' => [
        // How long facts stay active in working memory (months)
        'fact_expiry_months' => env('EXPERT_FACT_EXPIRY_MONTHS', 6),
        
        // Minimum confidence score for auto-processing
        'min_confidence_score' => env('EXPERT_MIN_CONFIDENCE', 0.5),
        
        // Enable auto-processing after approval
        'auto_process_on_approval' => env('EXPERT_AUTO_PROCESS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Notification (Fonnte)
    |--------------------------------------------------------------------------
    */
    'whatsapp' => [
        'enabled' => env('WHATSAPP_ENABLED', false),
        'api_url' => env('WHATSAPP_API_URL', 'https://api.fonnte.com/send'),
        'token' => env('WHATSAPP_TOKEN'),
        
        // Templates
        'templates' => [
            'diagnosis_notification' => "Assalamu'alaikum {wali_name},\n\nKami dari Habitify PP An-Nur ingin menginformasikan bahwa putra/putri Bapak/Ibu, {santri_name}, memerlukan perhatian khusus.\n\nDiagnosis: {diagnosis_name}\nRekomendasi: {rekomendasi}\n\nMohon untuk dapat berkoordinasi dengan Guru BK kami.\n\nTerima kasih.",
            
            'violation_notification' => "Assalamu'alaikum {wali_name},\n\nKami informasikan bahwa {santri_name} telah melakukan pelanggaran:\n\nJenis: {violation_name}\nPoin: {poin}\nKonsekuensi: {konsekuensi}\n\nTotal poin pelanggaran saat ini: {total_poin}\n\nMohon perhatiannya.",
            
            'appreciation_notification' => "Assalamu'alaikum {wali_name},\n\nKabar baik! {santri_name} mendapatkan apresiasi:\n\nJenis: {appreciation_name}\nPoin: +{poin}\n\nTotal poin apresiasi: {total_poin}\n\nSemoga menjadi motivasi untuk terus berprestasi.",
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Point Thresholds
    |--------------------------------------------------------------------------
    */
    'points' => [
        // Warning threshold for dashboard
        'warning_threshold' => 30,
        'danger_threshold' => 50,
        'critical_threshold' => 100,
    ],

    /*
    |--------------------------------------------------------------------------
    | Preprocessing Configuration
    |--------------------------------------------------------------------------
    */
    'preprocessing' => [
        // Use Sastrawi for stemming
        'use_sastrawi' => true,
        
        // Additional stop words
        'additional_stopwords' => [
            'pondok', 'asrama', 'santri', 'ustadz', 'ustadzah',
            'kamar', 'kelas', 'sekolah', 'madrasah',
        ],
        
        // Fuzzy matching threshold (levenshtein distance)
        'fuzzy_threshold' => 2,
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Settings
    |--------------------------------------------------------------------------
    */
    'app' => [
        'pondok_name' => env('PONDOK_NAME', 'Pondok Pesantren An-Nur'),
        'logo_path' => env('LOGO_PATH', '/images/logo.png'),
        
        // Kelas options
        'kelas_options' => ['1A', '1B', '2A', '2B', '3A', '3B'],
        
        // Kamar options
        'kamar_options' => [
            'Asrama Putra 1', 'Asrama Putra 2', 'Asrama Putra 3',
            'Asrama Putri 1', 'Asrama Putri 2', 'Asrama Putri 3',
        ],
    ],
];