<?php

namespace App\Exceptions;

class ComplaintNotAllowedException extends RecyclinkException
{
    protected $message = "Komplain tidak dapat diajukan untuk transaksi ini.";
    protected int $statusCode = 400;
}
