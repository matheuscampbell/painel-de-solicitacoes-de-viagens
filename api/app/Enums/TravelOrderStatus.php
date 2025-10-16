<?php

namespace App\Enums;

enum TravelOrderStatus: string
{
    case SOLICITADO = 'solicitado';
    case APROVADO = 'aprovado';
    case CANCELADO = 'cancelado';

    public static function values(): array
    {
        return array_map(static fn (self $status) => $status->value, self::cases());
    }

    public static function tryFromName(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        $value = strtolower($value);

        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        return null;
    }

    public function label(): string
    {
        return match ($this) {
            self::SOLICITADO => 'Solicitado',
            self::APROVADO => 'Aprovado',
            self::CANCELADO => 'Cancelado',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::SOLICITADO => in_array($target, [self::SOLICITADO, self::APROVADO, self::CANCELADO], true),
            self::APROVADO => $target === self::APROVADO,
            self::CANCELADO => $target === self::CANCELADO,
        };
    }
}
