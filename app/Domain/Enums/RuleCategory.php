<?php

declare(strict_types=1);

namespace Domain\Enums;

enum RuleCategory: string
{
    case KORBAN = 'korban';
    case PELAKU = 'pelaku';
    case INTERNAL = 'internal';

    public function label(): string
    {
        return match ($this) {
            self::KORBAN => 'Kategori Korban',
            self::PELAKU => 'Kategori Pelaku',
            self::INTERNAL => 'Kategori Internal',
        };
    }

    public function prefix(): string
    {
        return match ($this) {
            self::KORBAN => 'RA',
            self::PELAKU => 'RB',
            self::INTERNAL => 'RC',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::KORBAN => 'warning',
            self::PELAKU => 'danger',
            self::INTERNAL => 'info',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::KORBAN => 'Rule untuk santri yang menjadi korban (bullying, kekerasan, dll)',
            self::PELAKU => 'Rule untuk santri yang melakukan pelanggaran',
            self::INTERNAL => 'Rule untuk masalah internal santri (mental, keluarga, dll)',
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