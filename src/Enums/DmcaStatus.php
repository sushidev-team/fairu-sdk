<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Enums;

enum DmcaStatus: string
{
    case OPEN = 'OPEN';
    case ACCEPTED = 'ACCEPTED';
    case DENIES = 'DENIES';
    case FAILED = 'FAILED';
}
