<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Enums;

enum CustomDomainStatus: string
{
    case NONE = 'NONE';
    case CHECKING = 'CHECKING';
    case FAILED = 'FAILED';
    case SUCCESS = 'SUCCESS';
}
