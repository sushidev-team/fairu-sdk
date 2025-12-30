<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Enums;

enum DiskType: string
{
    case FTP = 'FTP';
    case SFTP = 'SFTP';
    case S3 = 'S3';
}
