<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Conversation;
use App\Models\WasteListing;
use App\Http\Requests\StartConversationRequest;
use App\Services\ChatService;
use App\Exceptions\RecyclinkException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class ConversationController extends Controller implements HasMiddleware
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

    // ponytail: view conversations list
    public function index(Request $request)
    {
        $conversations = $this->chatService->getUserConversations(auth()->user());

        if ($request->wantsJson()) {
            return response()->json($conversations);
        }

        return view('chat.index', compact('conversations'));
    }

    // ponytail: show single conversation and its messages
    public function show(Request $request, Conversation $conversation)
    {
        if ($conversation->buyer_id !== auth()->id() && $conversation->seller_id !== auth()->id()) {
            abort(403, 'Unauthorized conversation view.');
        }

        try {
            $this->chatService->markMessagesAsRead(auth()->user(), $conversation);
        } catch (RecyclinkException $e) {
            abort(403, $e->getMessage());
        }

        $messages = $conversation->messages()->with('sender')->oldest()->get();

        if ($request->wantsJson()) {
            return response()->json([
                'conversation' => $conversation->load(['buyer', 'seller', 'listing']),
                'messages' => $messages,
            ]);
        }

        return view('chat.show', compact('conversation', 'messages'));
    }

    // ponytail: start a new conversation via listing
    public function start(Request $request, WasteListing $wasteListing)
    {
        try {
            $conversation = $this->chatService->startConversation(
                auth()->user(),
                $wasteListing,
                $wasteListing->seller,
                $request->input('message')
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'conversation' => $conversation,
                ]);
            }

            return redirect()->route('conversations.show', $conversation)->with('success', 'Percakapan dimulai.');
        } catch (RecyclinkException $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ponytail: start conversation directly with target user (e.g. from store profile or order)
    public function startWithUser(Request $request, User $user)
    {
        try {
            $listingId = $request->input('listing_id');
            $listing = $listingId ? WasteListing::find($listingId) : null;

            $conversation = $this->chatService->startConversation(
                auth()->user(),
                $listing,
                $user,
                $request->input('message')
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'conversation' => $conversation,
                ]);
            }

            return redirect()->route('conversations.show', $conversation)->with('success', 'Percakapan dimulai.');
        } catch (RecyclinkException $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ponytail: hide conversation from current user's list (not a real delete — partner still sees it)
    public function destroy(Request $request, Conversation $conversation)
    {
        if ($conversation->buyer_id !== auth()->id() && $conversation->seller_id !== auth()->id()) {
            if ($request->wantsJson()) return response()->json(['error' => 'Unauthorized'], 403);
            return redirect()->route('conversations.index');
        }

        $conversation->hideFor(auth()->id());

        if ($request->wantsJson()) return response()->json(['success' => true]);
        return redirect()->route('conversations.index')->with('success', 'Percakapan dihapus dari daftar.');
    }
}
