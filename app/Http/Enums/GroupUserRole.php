<?php

namespace App\Http\Enums;

enum GroupUserRole :string
{
    case ADMIN = 'admin';
    case USER = 'user';
}