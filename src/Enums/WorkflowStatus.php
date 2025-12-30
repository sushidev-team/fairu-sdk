<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Enums;

enum WorkflowStatus: string
{
    case NONE = 'NONE';
    case TRIGGERED = 'TRIGGERED';
    case PROCESSING = 'PROCESSING';
    case FAILED = 'FAILED';
    case SUCCESS = 'SUCCESS';
}
