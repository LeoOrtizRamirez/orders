<?php

namespace App\Enums;

enum ProductUnit: string
{
    case UNIDAD = 'UNIDAD';
    case KILO = 'KILO';

    public function label(): string
    {
        return match($this) {
            self::UNIDAD => 'Unidad',
            self::KILO => 'Kilo',
        };
    }

    public static function options(): array
    {
        return array_map(fn($case) => [
            'id' => $case->value,
            'name' => $case->label()
        ], self::cases());
    }
}
