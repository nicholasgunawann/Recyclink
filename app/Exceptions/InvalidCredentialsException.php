<?php

namespace App\Exceptions;

class InvalidCredentialsException extends RecyclinkException
{
    public function __construct(string $message = "Kredensial login yang Anda masukkan tidak valid.")
    {
        parent::__construct($message, 422);
    }
}
