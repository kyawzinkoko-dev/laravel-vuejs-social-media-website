<?php

namespace App\Http\Enums;

enum ReactionEnum: string
{
    case LIKE = 'like';
    case HAPPY = 'happy';
    case SAD = 'sad';
}
