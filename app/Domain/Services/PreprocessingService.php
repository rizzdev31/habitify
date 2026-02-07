<?php

declare(strict_types=1);

namespace Domain\Services;

use App\Infrastructure\Persistence\Models\KbDictionary;
use App\Infrastructure\Persistence\Models\SantriProfile;
use Sastrawi\Stemmer\StemmerFactory;
use Sastrawi\StopWordRemover\StopWordRemoverFactory;

class PreprocessingService
{
    private $stemmer;
    private $stopWordRemover;
    private array $stopWords;

    public function __construct()
    {
        $stemmerFactory = new StemmerFactory();
        $this->stemmer = $stemmerFactory->createStemmer();

        $stopWordFactory = new StopWordRemoverFactory();
        $this->stopWordRemover = $stopWordFactory->createStopWordRemover();

        // Additional Indonesian stop words
        $this->stopWords = [
            'yang', 'di', 'ke', 'dari', 'ini', 'itu', 'dengan', 'untuk',
            'pada', 'adalah', 'dan', 'atau', 'juga', 'sudah', 'saya',
            'anda', 'akan', 'bisa', 'ada', 'tidak', 'kami', 'kita',
            'mereka', 'dia', 'ia', 'nya', 'tersebut', 'dalam', 'oleh',
            'sebagai', 'jika', 'maka', 'karena', 'saat', 'ketika',
            'setelah', 'sebelum', 'sedang', 'telah', 'lalu', 'kemudian',
            'dapat', 'harus', 'sangat', 'lebih', 'paling', 'seperti',
            'bahwa', 'agar', 'supaya', 'tetapi', 'namun', 'walau',
            'meski', 'bila', 'serta', 'maupun', 'atau', 'tadi', 'kelas',
            'sekolah', 'pondok', 'asrama', 'belakang', 'depan', 'atas',
        ];
    }

    /**
     * Main preprocessing pipeline
     */
    public function process(string $text): PreprocessingResult
    {
        $original = $text;

        // Step 1: Case Folding
        $cleaned = $this->caseFolding($text);

        // Step 2: Cleaning
        $cleaned = $this->cleaning($cleaned);

        // Step 3: Tokenization
        $tokens = $this->tokenize($cleaned);

        // Step 4: Detect Entities (Santri names) - BEFORE stopword removal
        $detectedEntities = $this->detectEntities($tokens);

        // Step 5: Stop Word Removal
        $tokensFiltered = $this->removeStopWords($tokens);

        // Step 6: Stemming
        $tokensStemmed = $this->stem($tokensFiltered);

        // Step 7: Knowledge Base Matching
        $matchingResult = $this->matchKnowledgeBase($tokensStemmed);

        // Step 8: Calculate Confidence
        $confidence = $this->calculateConfidence($matchingResult, $detectedEntities);

        return new PreprocessingResult(
            textOriginal: $original,
            textCleaned: $cleaned,
            tokens: $tokens,
            tokensStemmed: $tokensStemmed,
            detectedCodes: $matchingResult['codes'],
            detectedEntities: $detectedEntities,
            confidenceScore: $confidence,
            matchingDetails: $matchingResult['details']
        );
    }

    /**
     * Step 1: Convert to lowercase
     */
    public function caseFolding(string $text): string
    {
        return mb_strtolower($text, 'UTF-8');
    }

    /**
     * Step 2: Remove unwanted characters
     */
    public function cleaning(string $text): string
    {
        // Remove emojis
        $text = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $text);
        $text = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $text);
        $text = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $text);
        $text = preg_replace('/[\x{2600}-\x{26FF}]/u', '', $text);
        $text = preg_replace('/[\x{2700}-\x{27BF}]/u', '', $text);

        // Remove special characters but keep letters, numbers, spaces
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);

        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    /**
     * Step 3: Split into tokens
     */
    public function tokenize(string $text): array
    {
        $tokens = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        return array_values(array_filter($tokens, fn($t) => strlen($t) > 1));
    }

    /**
     * Step 4: Detect santri names using fuzzy matching
     */
    public function detectEntities(array $tokens): array
    {
        $entities = [];
        $santriNames = $this->getSantriNames();

        $text = implode(' ', $tokens);

        foreach ($santriNames as $santri) {
            // Check nama_panggilan first (more likely to be used)
            if ($santri['nama_panggilan']) {
                $namaPanggilan = mb_strtolower($santri['nama_panggilan']);
                if ($this->fuzzyMatch($text, $namaPanggilan)) {
                    $entities[] = [
                        'santri_id' => $santri['id'],
                        'nama_lengkap' => $santri['nama_lengkap'],
                        'nama_panggilan' => $santri['nama_panggilan'],
                        'matched_name' => $namaPanggilan,
                        'confidence' => $this->calculateNameConfidence($text, $namaPanggilan),
                    ];
                    continue;
                }
            }

            // Check nama lengkap
            $namaLengkap = mb_strtolower($santri['nama_lengkap']);
            $namaParts = explode(' ', $namaLengkap);

            foreach ($namaParts as $part) {
                if (strlen($part) > 2 && $this->fuzzyMatch($text, $part)) {
                    $entities[] = [
                        'santri_id' => $santri['id'],
                        'nama_lengkap' => $santri['nama_lengkap'],
                        'nama_panggilan' => $santri['nama_panggilan'],
                        'matched_name' => $part,
                        'confidence' => $this->calculateNameConfidence($text, $part),
                    ];
                    break;
                }
            }
        }

        return $entities;
    }

    /**
     * Step 5: Remove stop words
     */
    public function removeStopWords(array $tokens): array
    {
        return array_values(array_filter($tokens, function ($token) {
            return !in_array($token, $this->stopWords) && strlen($token) > 2;
        }));
    }

    /**
     * Step 6: Stem words to root form
     */
    public function stem(array $tokens): array
    {
        return array_map(function ($token) {
            return $this->stemmer->stem($token);
        }, $tokens);
    }

    /**
     * Step 7: Match against knowledge base dictionary
     */
    public function matchKnowledgeBase(array $tokensStemmed): array
    {
        $codes = [];
        $details = [];

        $dictionary = KbDictionary::active()->get()->groupBy('kata');

        foreach ($tokensStemmed as $position => $token) {
            if (isset($dictionary[$token])) {
                foreach ($dictionary[$token] as $entry) {
                    $code = $entry->kode_referensi;

                    if (!in_array($code, $codes)) {
                        $codes[] = $code;
                    }

                    $details[] = [
                        'kata_ditemukan' => $token,
                        'kata_stem' => $token,
                        'kode_referensi' => $code,
                        'tipe' => $entry->tipe,
                        'bobot' => $entry->bobot,
                        'position' => $position,
                    ];
                }
            }
        }

        return [
            'codes' => $codes,
            'details' => $details,
        ];
    }

    /**
     * Step 8: Calculate overall confidence score
     */
    private function calculateConfidence(array $matchingResult, array $entities): float
    {
        $codeCount = count($matchingResult['codes']);
        $entityCount = count($entities);
        $matchCount = count($matchingResult['details']);

        // Base confidence from matches
        $codeConfidence = min($codeCount * 0.2, 0.5);

        // Entity confidence
        $entityConfidence = $entityCount > 0 ? 0.3 : 0;

        // Match depth confidence
        $matchConfidence = min($matchCount * 0.05, 0.2);

        return round($codeConfidence + $entityConfidence + $matchConfidence, 2);
    }

    /**
     * Get all santri names for entity detection
     */
    private function getSantriNames(): array
    {
        return SantriProfile::active()
            ->select('id', 'nama_lengkap', 'nama_panggilan')
            ->get()
            ->toArray();
    }

    /**
     * Fuzzy string matching
     */
    private function fuzzyMatch(string $text, string $needle): bool
    {
        // Direct match
        if (str_contains($text, $needle)) {
            return true;
        }

        // Levenshtein distance for typos
        $words = explode(' ', $text);
        foreach ($words as $word) {
            if (strlen($word) >= strlen($needle) - 1) {
                $distance = levenshtein($word, $needle);
                if ($distance <= 2) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Calculate confidence for name matching
     */
    private function calculateNameConfidence(string $text, string $name): float
    {
        if (str_contains($text, $name)) {
            return 1.0;
        }

        $words = explode(' ', $text);
        foreach ($words as $word) {
            $distance = levenshtein($word, $name);
            if ($distance <= 2) {
                return 1 - ($distance * 0.15);
            }
        }

        return 0.5;
    }

    /**
     * Detect semantic role (pelaku/korban) based on verb prefixes
     */
    public function detectSemanticRole(string $text, array $entities): array
    {
        $result = [];

        // Active voice indicators (subject = pelaku)
        $activePatterns = [
            '/(\w+)\s+(me\w+|mem\w+|men\w+|meng\w+|meny\w+)\s+(\w+)/i',
        ];

        // Passive voice indicators (subject = korban)
        $passivePatterns = [
            '/(\w+)\s+(di\w+)\s+(oleh\s+)?(\w+)?/i',
        ];

        foreach ($entities as $entity) {
            $name = mb_strtolower($entity['matched_name']);
            $role = 'terlibat'; // default

            // Check if name appears before active verb
            foreach ($activePatterns as $pattern) {
                if (preg_match($pattern, $text, $matches)) {
                    $subject = mb_strtolower($matches[1] ?? '');
                    if ($this->fuzzyMatch($subject, $name)) {
                        $role = 'pelaku';
                        break;
                    }
                }
            }

            // Check if name appears before passive verb
            foreach ($passivePatterns as $pattern) {
                if (preg_match($pattern, $text, $matches)) {
                    $subject = mb_strtolower($matches[1] ?? '');
                    if ($this->fuzzyMatch($subject, $name)) {
                        $role = 'korban';
                        break;
                    }
                }
            }

            $result[] = [
                'santri_id' => $entity['santri_id'],
                'nama' => $entity['nama_lengkap'],
                'role' => $role,
                'confidence' => $entity['confidence'],
            ];
        }

        return $result;
    }
}

/**
 * Value Object for preprocessing result
 */
class PreprocessingResult
{
    public function __construct(
        public readonly string $textOriginal,
        public readonly string $textCleaned,
        public readonly array $tokens,
        public readonly array $tokensStemmed,
        public readonly array $detectedCodes,
        public readonly array $detectedEntities,
        public readonly float $confidenceScore,
        public readonly array $matchingDetails,
    ) {}

    public function toArray(): array
    {
        return [
            'text_original' => $this->textOriginal,
            'text_cleaned' => $this->textCleaned,
            'tokens' => $this->tokens,
            'tokens_stemmed' => $this->tokensStemmed,
            'detected_codes' => $this->detectedCodes,
            'detected_entities' => $this->detectedEntities,
            'confidence_score' => $this->confidenceScore,
            'matching_details' => $this->matchingDetails,
        ];
    }

    public function hasCodes(): bool
    {
        return count($this->detectedCodes) > 0;
    }

    public function hasEntities(): bool
    {
        return count($this->detectedEntities) > 0;
    }

    public function getPelanggaranCodes(): array
    {
        return array_filter($this->detectedCodes, fn($code) => str_starts_with($code, 'P'));
    }

    public function getApresiasiCodes(): array
    {
        return array_filter($this->detectedCodes, fn($code) => str_starts_with($code, 'A'));
    }

    public function getKonselorCodes(): array
    {
        return array_filter($this->detectedCodes, fn($code) => str_starts_with($code, 'G'));
    }
}