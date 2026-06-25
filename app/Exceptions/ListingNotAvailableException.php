<?php

namespace App\Exceptions;

class ListingNotAvailableException extends RecyclinkException
{
    protected $message = "Listing limbah tidak tersedia untuk dipesan.";
    protected int $statusCode = 422;
}
