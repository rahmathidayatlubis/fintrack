<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'icon', 'color', 'type', 'is_default'];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public static function getDefaults(): array
    {
        return [
            // Income categories
            ['name' => 'Gaji',           'icon' => 'briefcase',          'color' => '#10B981', 'type' => 'income',   'is_default' => true],
            ['name' => 'Bonus',          'icon' => 'gift',               'color' => '#059669', 'type' => 'income',   'is_default' => true],
            ['name' => 'Investasi',      'icon' => 'chart-bar',          'color' => '#34D399', 'type' => 'income',   'is_default' => true],
            ['name' => 'Freelance',      'icon' => 'computer-desktop',   'color' => '#6EE7B7', 'type' => 'income',   'is_default' => true],
            ['name' => 'Lainnya',        'icon' => 'plus-circle',        'color' => '#A7F3D0', 'type' => 'income',   'is_default' => true],
            // Expense categories
            ['name' => 'Makan & Minum',  'icon' => 'cake',               'color' => '#EF4444', 'type' => 'expense',  'is_default' => true],
            ['name' => 'Transportasi',   'icon' => 'truck',              'color' => '#F59E0B', 'type' => 'expense',  'is_default' => true],
            ['name' => 'Belanja',        'icon' => 'shopping-bag',       'color' => '#8B5CF6', 'type' => 'expense',  'is_default' => true],
            ['name' => 'Tagihan',        'icon' => 'document-text',      'color' => '#EC4899', 'type' => 'expense',  'is_default' => true],
            ['name' => 'Kesehatan',      'icon' => 'heart',              'color' => '#F43F5E', 'type' => 'expense',  'is_default' => true],
            ['name' => 'Hiburan',        'icon' => 'film',               'color' => '#14B8A6', 'type' => 'expense',  'is_default' => true],
            ['name' => 'Pendidikan',     'icon' => 'academic-cap',       'color' => '#3B82F6', 'type' => 'expense',  'is_default' => true],
            ['name' => 'Lainnya',        'icon' => 'ellipsis-horizontal','color' => '#6B7280', 'type' => 'expense',  'is_default' => true],
            // Transfer
            ['name' => 'Transfer',       'icon' => 'arrows-right-left',  'color' => '#6366F1', 'type' => 'transfer', 'is_default' => true],
        ];
    }
}
