<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'ADMIN';
    case USER = 'USER';
    case PUBLIC = "PUBLIC";
}
