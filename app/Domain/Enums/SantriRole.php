<?php

declare(strict_types=1);

namespace Domain\Enums;

enum SantriRole: string
{
    case PELAKU = 'pelaku';
    case KORBAN = 'korban';
    case TERLIBAT = 'terlibat';

    public function label(): string
    {
        return match ($this) {
            self::PELAKU => 'Pelaku',
            self::KORBAN => 'Korban',
            self::TERLIBAT => 'Terlibat',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PELAKU => 'danger',
            self::KORBAN => 'warning',
            self::TERLIBAT => 'info',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PELAKU => 'Santri yang melakukan tindakan',
            self::KORBAN => 'Santri yang menjadi korban',
            self::TERLIBAT => 'Santri yang terlibat dalam kejadian',
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