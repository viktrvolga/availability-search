<?php

declare(strict_types=1);

namespace App\Common\Structures;

enum TimeOfDay: string
{
    case ANY = 'any';
    case MORNING = 'morning';
    case LUNCH = 'lunch';
    case EVENING = "evening";
    case NIGHT = "night";
}
