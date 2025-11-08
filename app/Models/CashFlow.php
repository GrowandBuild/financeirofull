<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashFlow extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'description',
        'amount',
        'category_id',
        'goal_category',
        'transaction_date',
        'payment_method',
        'reference',
        'is_recurring',
        'recurring_config',
        'is_confirmed'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'is_recurring' => 'boolean',
        'is_confirmed' => 'boolean',
        'recurring_config' => 'array'
    ];

    // Relacionamentos
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class, 'cashflow_id');
    }

    // Scopes
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('is_confirmed', true);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }

    public function getTypeLabelAttribute()
    {
        return $this->type === 'income' ? 'Receita' : 'Despesa';
    }

    public function getPaymentMethodLabelAttribute()
    {
        $methods = [
            'cash' => 'Dinheiro',
            'card' => 'Cartão',
            'pix' => 'PIX',
            'transfer' => 'Transferência'
        ];

        return $methods[$this->payment_method] ?? 'Não informado';
    }
}
