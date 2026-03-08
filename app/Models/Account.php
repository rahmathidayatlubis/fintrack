<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'account_number', 'account_holder',
        'bank_name', 'type', 'icon', 'color', 'balance',
        'initial_balance', 'currency', 'description',
        'is_active', 'include_in_total', 'sort_order',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'initial_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'include_in_total' => 'boolean',
    ];

    // Account types
    const TYPES = [
        'cash' => ['label' => 'Tunai / Cash',    'icon' => 'banknotes'],
        'bank' => ['label' => 'Rekening Bank',   'icon' => 'building-library'],
        'e_wallet' => ['label' => 'Dompet Digital',  'icon' => 'device-phone-mobile'],
        'investment' => ['label' => 'Investasi',       'icon' => 'chart-bar-square'],
        'savings' => ['label' => 'Tabungan',        'icon' => 'archive-box'],
        'credit' => ['label' => 'Kartu Kredit',    'icon' => 'credit-card'],
        'debt' => ['label' => 'Hutang/ Pinjaman',    'icon' => 'credit-card'],
    ];

    // Preset accounts with icons & colors
    const PRESETS = [
        'BRI' => ['color' => '#003d9e', 'icon' => 'bank'],
        'BCA' => ['color' => '#005CA8', 'icon' => 'bank'],
        'Mandiri' => ['color' => '#0077B5', 'icon' => 'bank'],
        'BNI' => ['color' => '#F37021', 'icon' => 'bank'],
        'BSI' => ['color' => '#009A44', 'icon' => 'bank'],
        'DANA' => ['color' => '#108EE9', 'icon' => 'wallet'],
        'GoPay' => ['color' => '#00AA13', 'icon' => 'wallet'],
        'OVO' => ['color' => '#4C3494', 'icon' => 'wallet'],
        'ShopeePay' => ['color' => '#EE4D2D', 'icon' => 'wallet'],
        'Cash' => ['color' => '#059669', 'icon' => 'banknotes'],
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(Transaction::class, 'account_id')->where('type', 'transfer');
    }

    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(Transaction::class, 'destination_account_id')->where('type', 'transfer');
    }

    public function getFormattedBalanceAttribute(): string
    {
        return 'Rp '.number_format($this->balance, 0, ',', '.');
    }

    public function getTypeIconAttribute(): string
    {
        return self::TYPES[$this->type]['icon'] ?? 'wallet';
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type]['label'] ?? $this->type;
    }

    // Recalculate balance from transactions
    public function recalculateBalance(): void
    {
        $balance = $this->initial_balance;

        $this->transactions()
            ->whereNull('deleted_at')
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->each(function ($transaction) use (&$balance) {
                if ($transaction->type === 'income') {
                    $balance += $transaction->amount;
                } elseif ($transaction->type === 'expense') {
                    $balance -= ($transaction->amount + $transaction->admin_fee);
                } elseif ($transaction->type === 'transfer') {
                    if ($transaction->transfer_type === 'debit') {
                        $balance -= ($transaction->amount + $transaction->admin_fee);
                    } else {
                        $balance += $transaction->amount;
                    }
                } elseif ($transaction->type === 'adjustment') {
                    $balance = $transaction->amount; // Set langsung
                }
            });

        $this->update(['balance' => $balance]);
    }
}
