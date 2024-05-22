<?php

namespace App\Enums;

enum StatusEnum: string
{
    case Active = 'A';
    case Completed = 'C';
    case Hold = 'H';
    case Cancelled = 'X';

}
