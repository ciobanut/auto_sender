<?php

namespace App\Enums;

enum CoverLetterStatus: string
{
    case Draft = 'draft';
    case Edited = 'edited';
    case Approved = 'approved';
    case Sent = 'sent';
}
