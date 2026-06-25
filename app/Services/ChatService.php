<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\WasteListing;
use App\Models\User;
use App\Exceptions\RecyclinkException;
use App\Exceptions\UnauthorizedBusinessActionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ChatService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    // ponytail: start a new conversation
    public function startConversation(User $buyer, WasteListing $listing, ?string $message = null): Conversation
    {
        if ($listing->seller_id === $buyer->id) {
            throw new UnauthorizedBusinessActionException("You cannot start a chat with yourself.");
        }

        return DB::transaction(function () use ($buyer, $listing, $message) {
            $conversation = Conversation::firstOrCreate([
                'listing_id' => $listing->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $listing->seller_id,
            ]);

            $conversation->update(['last_message_at' => now()]);

            if ($message) {
                $conversation->messages()->create([
                    'sender_id' => $buyer->id,
                    'message_text' => $message,
                    'message_type' => 'text',
                    'is_read' => false,
                ]);

                $seller = $listing->seller;
                if ($seller) {
                    $this->notificationService->sendToUser(
                        $seller,
                        "New Message",
                        "{$buyer->name} sent a message regarding '{$listing->title}': {$message}",
                        "chat",
                        $conversation->id
                    );
                }
            }

            return $conversation;
        });
    }

    // ponytail: send message in active conversation
    public function sendMessage(User $sender, Conversation $conversation, array $data): Message
    {
        if ($conversation->buyer_id !== $sender->id && $conversation->seller_id !== $sender->id) {
            throw new UnauthorizedBusinessActionException("Unauthorized chat action.");
        }

        return DB::transaction(function () use ($conversation, $sender, $data) {
            $messageText = $data['message_text'] ?? $data['message'] ?? null;
            $messageType = $data['message_type'] ?? 'text';
            $attachmentPath = null;
            $attachmentType = null;

            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $attachmentPath = $data['image']->store('messages', 'public');
                $attachmentType = 'image';
                $messageType = 'image';
            }

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $sender->id,
                'message_text' => $messageText,
                'message_type' => $messageType,
                'is_read' => false,
                'attachment_path' => $attachmentPath,
                'attachment_type' => $attachmentType,
            ]);

            $conversation->update(['last_message_at' => now()]);

            $recipientId = ($conversation->buyer_id === $sender->id) ? $conversation->seller_id : $conversation->buyer_id;
            $recipient = User::find($recipientId);
            if ($recipient) {
                $this->notificationService->sendToUser(
                    $recipient,
                    "New Message from {$sender->name}",
                    $messageText ?? "Sent an image",
                    "chat",
                    $conversation->id
                );
            }

            return $message;
        });
    }

    // ponytail: mark unread messages as read
    public function markMessagesAsRead(User $user, Conversation $conversation): void
    {
        if ($conversation->buyer_id !== $user->id && $conversation->seller_id !== $user->id) {
            throw new UnauthorizedBusinessActionException("Unauthorized chat action.");
        }

        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    // ponytail: retrieve paginated conversations for a user
    public function getUserConversations(User $user): LengthAwarePaginator
    {
        return Conversation::where('buyer_id', $user->id)
            ->orWhere('seller_id', $user->id)
            ->with(['buyer', 'seller', 'listing', 'latestMessage'])
            ->latest('last_message_at')
            ->paginate(20);
    }
}
