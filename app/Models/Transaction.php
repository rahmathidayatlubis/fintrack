<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'account_id', 'category_id', 'destination_account_id',
        'type', 'amount', 'admin_fee', 'balance_before', 'balance_after',
        'reference_code', 'recipient_name', 'recipient_account',
        'description', 'notes', 'transaction_date',
        'transfer_pair_id', 'transfer_type', 'is_confirmed'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'transaction_date' => 'datetime',
        'is_confirmed' => 'boolean',
    ];

    const TYPES = [
        'income'     => ['label' => 'Pemasukan',   'color' => 'green',  'icon' => 'arrow-down-left'],
        'expense'    => ['label' => 'Pengeluaran',  'color' => 'red',    'icon' => 'arrow-up-right'],
        'transfer'   => ['label' => 'Transfer',     'color' => 'blue',   'icon' => 'arrows-right-left'],
        'adjustment' => ['label' => 'Penyesuaian',  'color' => 'orange', 'icon' => 'adjustments-horizontal'],
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function destinationAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'destination_account_id');
    }

    public function transferPair(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transfer_pair_id');
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->amount + $this->admin_fee;
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getSignedAmountAttribute(): float
    {
        if ($this->type === 'income') return $this->amount;
        if ($this->type === 'expense') return -($this->amount + $this->admin_fee);
        if ($this->type === 'transfer') {
            return $this->transfer_type === 'debit'
                ? -($this->amount + $this->admin_fee)
                : $this->amount;
        }
        return 0;
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type]['label'] ?? $this->type;
    }

    public function getTypeColorAttribute(): string
    {
        return self::TYPES[$this->type]['color'] ?? 'gray';
    }

    public function getTypeIconAttribute(): string
    {
        return self::TYPES[$this->type]['icon'] ?? 'currency-dollar';
    }

    public function isIncome(): bool
    {
        return $this->type === 'income' ||
               ($this->type === 'transfer' && $this->transfer_type === 'credit');
    }

    public function isExpense(): bool
    {
        return $this->type === 'expense' ||
               ($this->type === 'transfer' && $this->transfer_type === 'debit');
    }

    public function scopeForAccount($query, int $accountId)
    {
        return $query->where(function ($q) use ($accountId) {
            $q->where('account_id', $accountId)
              ->orWhere('destination_account_id', $accountId);
        });
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('transaction_date', now()->month)
                     ->whereYear('transaction_date', now()->year);
    }
}
