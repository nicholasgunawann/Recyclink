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

    // ponytail: start a new conversation with a listing or user directly
    public function startConversation(User $initiator, ?WasteListing $listing = null, ?User $targetUser = null, ?string $message = null): Conversation
    {
        if ($listing && !$targetUser) {
            $targetUser = $listing->seller;
        }

        if (!$targetUser) {
            throw new UnauthorizedBusinessActionException("Pengguna tujuan chat tidak ditemukan.");
        }

        if ($initiator->id === $targetUser->id) {
            throw new UnauthorizedBusinessActionException("Anda tidak dapat memulai chat dengan diri sendiri.");
        }

        // Tentukan buyer dan seller dalam relasi percakapan
        if ($initiator->isSeller() && $targetUser->isBuyer()) {
            $buyerId = $targetUser->id;
            $sellerId = $initiator->id;
        } else {
            $buyerId = $initiator->id;
            $sellerId = $targetUser->id;
        }

        return DB::transaction(function () use ($initiator, $targetUser, $listing, $buyerId, $sellerId, $message) {
            // Cari percakapan yang sudah ada antara kedua pengguna ini
            $query = Conversation::where(function($q) use ($buyerId, $sellerId) {
                $q->where('buyer_id', $buyerId)->where('seller_id', $sellerId);
            })->orWhere(function($q) use ($buyerId, $sellerId) {
                $q->where('buyer_id', $sellerId)->where('seller_id', $buyerId);
            });

            if ($listing) {
                $conversation = (clone $query)->where('listing_id', $listing->id)->first();
            } else {
                $conversation = $query->first();
            }

            if (!$conversation) {
                $conversation = Conversation::create([
                    'listing_id' => $listing?->id,
                    'buyer_id' => $buyerId,
                    'seller_id' => $sellerId,
                    'last_message_at' => now(),
                ]);
            } else {
                $conversation->update(['last_message_at' => now()]);
            }

            if ($message) {
                $conversation->messages()->create([
                    'sender_id' => $initiator->id,
                    'message_text' => $message,
                    'message_type' => 'text',
                    'is_read' => false,
                ]);

                $this->notificationService->sendToUser(
                    $targetUser,
                    "Pesan Baru dari {$initiator->name}",
                    $message,
                    "chat",
                    $conversation->id
                );
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

            $recipient = ($conversation->buyer_id === $sender->id) ? $conversation->seller : $conversation->buyer;
            if ($recipient) {
                $this->notificationService->sendToUser(
                    $recipient,
                    "Pesan Baru dari {$sender->name}",
                    $messageText ?? "Mengirimkan gambar",
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
