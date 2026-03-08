<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Debt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'transaction_id', 'recipient_name', 'recipient_account',
        'recipient_bank', 'original_amount', 'paid_amount', 'adjusted_amount',
        'status', 'notes', 'due_date', 'paid_at',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'adjusted_amount' => 'decimal:2',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
    ];

    const STATUS = [
        'unpaid' => ['label' => 'Belum Lunas', 'color' => 'red'],
        'partial' => ['label' => 'Sebagian',    'color' => 'orange'],
        'paid' => ['label' => 'Lunas',        'color' => 'green'],
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(DebtPayment::class)->orderByDesc('paid_at');
    }

    // Nominal aktif hutang (setelah penyesuaian)
    public function getEffectiveAmountAttribute(): float
    {
        return $this->adjusted_amount ?? $this->original_amount;
    }

    // Sisa hutang
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->effective_amount - $this->paid_amount);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status]['label'] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS[$this->status]['color'] ?? 'gray';
    }

    public function getFormattedEffectiveAmountAttribute(): string
    {
        return 'Rp '.number_format($this->effective_amount, 0, ',', '.');
    }

    public function getFormattedRemainingAmountAttribute(): string
    {
        return 'Rp '.number_format($this->remaining_amount, 0, ',', '.');
    }

    // Recalculate status berdasarkan paid_amount
    public function recalculateStatus(): void
    {
        $remaining = $this->remaining_amount;

        if ($remaining <= 0) {
            $this->update([
                'status' => 'paid',
                'paid_at' => $this->paid_at ?? now(),
            ]);
        } elseif ($this->paid_amount > 0) {
            $this->update(['status' => 'partial', 'paid_at' => null]);
        } else {
            $this->update(['status' => 'unpaid', 'paid_at' => null]);
        }
    }

    // Cek apakah hutang sudah jatuh tempo
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date &&
               $this->due_date->isPast() &&
               $this->status !== 'paid';
    }

    // Hitung persentase pembayaran untuk progress bar
    public function getPaymentPercentageAttribute(): float
    {
        if ($this->effective_amount <= 0) {
            return 0;
        }

        return min(100, round(($this->paid_amount / $this->effective_amount) * 100));
    }
}
