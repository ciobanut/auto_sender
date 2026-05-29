<?php

namespace App\Enums;

enum JobLinkStatus: string
{
    case New = 'new';
    case ReFetched = 're_fetched';
    case Processed = 'processed';
    case Ignored = 'ignored';
}
