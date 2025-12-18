<?php

namespace App\Enums;

enum ProductCategory: string
{
    case FRUTAS = 'FRUTAS';
    case VERDURAS = 'VERDURAS';
    case TUBERCULOS = 'TUBERCULOS';
    case JUGOS = 'JUGOS';
    case PULPAS = 'PULPAS';
    case OTROS = 'OTROS';

    public function label(): string
    {
        return match($this) {
            self::FRUTAS => '01: FRUTAS',
            self::VERDURAS => '02: VERDURAS',
            self::TUBERCULOS => '03: TUBERCULOS',
            self::JUGOS => '04: JUGOS',
            self::PULPAS => '05: PULPAS',
            self::OTROS => '99: OTROS',
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
