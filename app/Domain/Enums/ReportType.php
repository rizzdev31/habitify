<?php

declare(strict_types=1);

namespace Domain\Enums;

enum ReportType: string
{
    case PELANGGARAN = 'pelanggaran';
    case APRESIASI = 'apresiasi';
    case KONSELING = 'konseling';

    public function label(): string
    {
        return match ($this) {
            self::PELANGGARAN => 'Pelanggaran',
            self::APRESIASI => 'Apresiasi',
            self::KONSELING => 'Konseling',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PELANGGARAN => 'danger',
            self::APRESIASI => 'success',
            self::KONSELING => 'info',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PELANGGARAN => 'heroicon-o-exclamation-triangle',
            self::APRESIASI => 'heroicon-o-star',
            self::KONSELING => 'heroicon-o-chat-bubble-left-right',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PELANGGARAN => 'Laporan terkait pelanggaran tata tertib santri',
            self::APRESIASI => 'Laporan terkait pencapaian atau perilaku positif santri',
            self::KONSELING => 'Laporan terkait kondisi mental atau kebutuhan konseling santri',
        };
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getLabels(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn($case) => $case->label(), self::cases())
        );
    }
}