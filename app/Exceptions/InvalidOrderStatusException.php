<?php

namespace App\Exceptions;

class InvalidOrderStatusException extends RecyclinkException
{
    protected $message = "Status pesanan tidak valid untuk aksi ini.";
    protected int $statusCode = 400;
}
