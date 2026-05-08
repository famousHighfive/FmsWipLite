<?php

namespace App\Enums;

/**
 * Statuts d'une affectation
 */
enum AssignmentStatus: string
{
    case ACTIVE = 'actif';
    case COMPLETED = 'termine';
    case SUSPENDED = 'suspendu';
}
