<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'client_name',
        'client_phone',
        'lead_source',
        'details',
        'total_amount',
        'commission_rate',
        'commission_amount',
        'transaction_date',
        'payment_status',
        'created_by',
        'pic_user_id',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'total_amount' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'commission_amount' => 'decimal:2',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }

    public function cashflows(): HasMany
    {
        return $this->hasMany(Cashflow::class);
    }
}
