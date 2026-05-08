<?php

namespace App\Enums;

/**
 * Codes des postes hiérarchiques
 */
enum PositionCode: string
{
    case CP = 'CP';   // Chef de Plateau
    case SUP = 'SUP'; // Superviseur
    case TC = 'TC';   // Téléconseiller
    case RH = 'RH';   // Ressources Humaines
}
