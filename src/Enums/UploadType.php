<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Enums;

enum UploadType: string
{
    case STANDARD = 'STANDARD';
    case DOWNLOAD = 'DOWNLOAD';
}
