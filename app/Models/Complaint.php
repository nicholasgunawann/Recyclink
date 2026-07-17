<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Support\Facades\Cache;

class Complaint extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        $clearCache = function () {
            // ponytail: invalidate admin dashboard cache on complaint changes
//             Cache::forget('admin_dashboard_summary');
        };

        static::created($clearCache);
        static::updated($clearCache);
        static::deleted($clearCache);
    }

    public const STATUS_OPEN = 'open';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'order_id', 'complainant_id', 'admin_id', 'complaint_type',
        'description', 'evidence_url', 'status', 'resolution_note',
        // ponytail: keep — respondent + tracking fields needed for dispute flow
        'respondent_id', 'complaint_number', 'subject', 'resolved_at',
    ];

    protected function casts(): array { return ['resolved_at' => 'datetime']; }

    public function isOpen(): bool     { return $this->status === self::STATUS_OPEN; }
    public function isResolved(): bool { return in_array($this->status, [self::STATUS_RESOLVED, 'closed']); }

    public function order(): BelongsTo       { return $this->belongsTo(Order::class); }
    public function complainant(): BelongsTo { return $this->belongsTo(User::class, 'complainant_id'); }
    public function admin(): BelongsTo       { return $this->belongsTo(User::class, 'admin_id'); }
    public function respondent(): BelongsTo  { return $this->belongsTo(User::class, 'respondent_id'); }
    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany { return $this->hasMany(ComplaintMessage::class); }
}
