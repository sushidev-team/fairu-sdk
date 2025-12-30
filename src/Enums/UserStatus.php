<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Enums;

enum UserStatus: string
{
    case CREATED = 'CREATED';
    case PENDING = 'PENDING';
    case ACCEPTED = 'ACCEPTED';
    case DECLINED = 'DECLINED';
}
