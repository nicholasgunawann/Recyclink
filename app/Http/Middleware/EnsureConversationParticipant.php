<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Conversation;

class EnsureConversationParticipant
{
    // ponytail: restrict chat access only to related buyer or seller
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $conversation = $request->route('conversation');

        if (is_scalar($conversation)) {
            $conversation = Conversation::find($conversation);
        }

        if ($conversation && $user) {
            if ($user->id === $conversation->buyer_id || $user->id === $conversation->seller_id) {
                return $next($request);
            }
            abort(403, 'Unauthorized conversation action.');
        }

        return $next($request);
    }
}
