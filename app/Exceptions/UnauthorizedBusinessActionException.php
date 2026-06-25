<?php

namespace App\Exceptions;

class UnauthorizedBusinessActionException extends RecyclinkException
{
    protected $message = "Anda tidak memiliki akses untuk melakukan aksi ini.";
    protected int $statusCode = 403;
}
