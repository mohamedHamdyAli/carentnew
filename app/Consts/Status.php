<?php

namespace App\Consts;

class Status
{
    public const CREATED            = 1;
    public const ACCEPTED           = 2;
    public const PENDING_PAYMENT    = 3;
    public const PAID               = 4;
    public const CONFIRMED          = 5;
    public const CAR_ARRIVED        = 6;
    public const CAR_DELIVERED      = 7;
    public const CAR_RETURNED       = 8;
    public const COMPLETED          = 9;
    public const REJECTED           = 10;
    public const CANCELED           = 11;
    public const REFUNDED           = 12;
}
