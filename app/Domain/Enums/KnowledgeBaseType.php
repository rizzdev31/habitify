<?php

declare(strict_types=1);

namespace Domain\Enums;

enum KnowledgeBaseType: string
{
    case PELANGGARAN = 'pelanggaran';
    case APRESIASI = 'apresiasi';
    case KONSELOR = 'konselor';

    public function label(): string
    {
        return match ($this) {
            self::PELANGGARAN => 'Pelanggaran',
            self::APRESIASI => 'Apresiasi',
            self::KONSELOR => 'Konselor (Gangguan)',
        };
    }

    public function prefix(): string
    {
        return match ($this) {
            self::PELANGGARAN => 'P',
            self::APRESIASI => 'A',
            self::KONSELOR => 'G',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PELANGGARAN => 'danger',
            self::APRESIASI => 'success',
            self::KONSELOR => 'info',
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