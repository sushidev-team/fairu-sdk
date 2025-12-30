<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Enums;

enum PdfSignatureRequestStatus: string
{
    case CREATED = 'CREATED';
    case STARTED = 'STARTED';
    case FAILED = 'FAILED';
    case CANCELED = 'CANCELED';
    case DONE = 'DONE';
}
