<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['listing_id', 'buyer_id', 'seller_id', 'last_message_at', 'hidden_by'];

    protected function casts(): array { return ['last_message_at' => 'datetime', 'hidden_by' => 'array']; }

    // ponytail: hide this conversation for a specific user
    public function hideFor(int $userId): void
    {
        $hidden = $this->hidden_by ?? [];
        if (!in_array($userId, $hidden)) {
            $this->update(['hidden_by' => array_values(array_merge($hidden, [$userId]))]);
        }
    }

    // ponytail: unhide when new message comes in
    public function unhideFor(int $userId): void
    {
        $hidden = $this->hidden_by ?? [];
        $this->update(['hidden_by' => array_values(array_filter($hidden, fn($id) => $id !== $userId))]);
    }

    public function listing(): BelongsTo  { return $this->belongsTo(WasteListing::class); }
    public function buyer(): BelongsTo    { return $this->belongsTo(User::class, 'buyer_id'); }
    public function seller(): BelongsTo   { return $this->belongsTo(User::class, 'seller_id'); }
    public function messages(): HasMany   { return $this->hasMany(Message::class); }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }
}
