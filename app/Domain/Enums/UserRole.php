<?php

declare(strict_types=1);

namespace Domain\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case BK = 'bk';
    case PENGAJAR = 'pengajar';
    case WALI = 'wali';
    case SANTRI = 'santri';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::BK => 'Guru BK',
            self::PENGAJAR => 'Pengajar',
            self::WALI => 'Wali Santri',
            self::SANTRI => 'Santri',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'danger',
            self::BK => 'warning',
            self::PENGAJAR => 'info',
            self::WALI => 'success',
            self::SANTRI => 'primary',
        };
    }

    public function panelPath(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'superadmin',
            self::BK => 'bk',
            self::PENGAJAR => 'pengajar',
            self::WALI => 'wali',
            self::SANTRI => 'santri',
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