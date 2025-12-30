<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Enums;

enum WebhookType: string
{
    case NONE = 'NONE';
    case BASIC = 'BASIC';
    case BEARER = 'BEARER';
}
