<?php

namespace App\Enums;

enum PaymentMethodEnum: int
{
    case CREDIT_CARD = 1;
    case DEBIT_CARD = 2;
    case PIX = 3;
}
