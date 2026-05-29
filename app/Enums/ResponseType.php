<?php

namespace App\Enums;

enum ResponseType: string
{
    case Rejected = 'rejected';
    case Interview = 'interview';
    case NoReply = 'no_reply';
}
