<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Enums;

enum UploadShareLinkExpiration: string
{
    case ONE_HOUR = 'ONE_HOUR';
    case SIX_HOURS = 'SIX_HOURS';
    case ONE_DAY = 'ONE_DAY';
    case SEVEN_DAYS = 'SEVEN_DAYS';
    case THIRTY_DAYS = 'THIRTY_DAYS';
    case NEVER = 'NEVER';
}
