<?php

namespace App\Enums;

enum ReferentielStatut:string{
    case ACTIF = 'Actif';
    case INACTIF = 'Inactif';
    case ARCHIVE = 'Archivé';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}