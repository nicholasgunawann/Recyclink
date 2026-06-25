<?php

namespace App\Exceptions;

class InsufficientWalletBalanceException extends RecyclinkException
{
    protected $message = "Saldo penjual tidak mencukupi untuk penarikan.";
    protected int $statusCode = 422;
}
