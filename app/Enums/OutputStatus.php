<?php

namespace App\Enums;

enum OutputStatus: string
{
    case Draft = 'draft';
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case Published = 'published';
}
