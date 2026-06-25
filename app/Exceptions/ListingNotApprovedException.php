<?php

namespace App\Exceptions;

class ListingNotApprovedException extends RecyclinkException
{
    protected $message = "Listing limbah belum disetujui oleh admin.";
    protected int $statusCode = 422;
}
