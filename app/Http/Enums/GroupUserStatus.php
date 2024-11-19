<?php
namespace App\Http\Enums;

enum GroupUserStatus :string{
    case APPROVED = 'approved';
   case PENDING = 'pending';
   case REJECT = 'reject';

}