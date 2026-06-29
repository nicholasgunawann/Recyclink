<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Http\Requests\StoreMessageRequest;
use App\Services\ChatService;
use App\Exceptions\RecyclinkException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class MessageController extends Controller implements HasMiddleware
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public static function middleware(): array
    {
        return [
            'auth',
        ];
    }

    // ponytail: send message in conversation
    public function store(StoreMessageRequest $request, Conversation $conversation)
    {
        try {
            $message = $this->chatService->sendMessage(auth()->user(), $conversation, $request->validated());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message->load('sender'),
                ]);
            }

            return redirect()->back()->with('success', 'Message sent.');
        } catch (RecyclinkException $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
