<?php

namespace App\Exceptions;

class InsufficientListingQuantityException extends RecyclinkException
{
    protected $message = "Jumlah limbah yang dipesan melebihi stok yang tersedia.";
    protected int $statusCode = 422;
}
