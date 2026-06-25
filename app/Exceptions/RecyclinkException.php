<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecyclinkException extends Exception
{
    protected int $statusCode = 400;

    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
    {
        $message = $message ?: $this->message;
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    // ponytail: transparently render redirect back or JSON depending on request type
    public function render(Request $request): Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $this->getMessage(),
            ], $this->statusCode);
        }

        return redirect()->back()->with('error', $this->getMessage());
    }
}
