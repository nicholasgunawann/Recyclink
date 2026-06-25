<?php

namespace App\Exceptions;

class PaymentAlreadyProcessedException extends RecyclinkException
{
    protected $message = "Pembayaran untuk pesanan ini sudah diproses.";
    protected int $statusCode = 400;
}
