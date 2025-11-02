<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialSchedule extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'description',
        'amount',
        'category_id',
        'goal_category',
        'scheduled_date',
        'image_path',
        'is_confirmed',
        'confirmed_at',
        'is_recurring',
        'recurring_config',
        'is_cancelled',
        'cancelled_at',
        'cancellation_reason'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'scheduled_date' => 'date',
        'is_confirmed' => 'boolean',
        'confirmed_at' => 'datetime',
        'is_recurring' => 'boolean',
        'recurring_config' => 'array',
        'is_cancelled' => 'boolean',
        'cancelled_at' => 'datetime'
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

    // Scopes
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopePending($query)
    {
        return $query->where('is_confirmed', false);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('is_confirmed', true);
    }

    public function scopeByMonth($query, $month, $year)
    {
        return $query->whereYear('scheduled_date', $year)
                    ->whereMonth('scheduled_date', $month);
    }

    public function scopeUpcoming($query, $days = 30)
    {
        return $query->where('scheduled_date', '<=', now()->addDays($days))
                    ->where('is_confirmed', false);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeCancelled($query)
    {
        return $query->where('is_cancelled', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_cancelled', false);
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

    public function isOverdue(): bool
    {
        return !$this->is_confirmed && $this->scheduled_date < now()->startOfDay();
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'is_cancelled' => true,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason
        ]);
    }

    public function getRecurringLabelAttribute()
    {
        if (!$this->is_recurring) {
            return 'Único';
        }

        $config = $this->recurring_config ?? [];
        $frequency = $config['frequency'] ?? 'monthly';
        
        $labels = [
            'daily' => 'Diário',
            'weekly' => 'Semanal',
            'biweekly' => 'Quinzenal',
            'monthly' => 'Mensal',
            'quarterly' => 'Trimestral',
            'semiannual' => 'Semestral',
            'yearly' => 'Anual'
        ];

        return $labels[$frequency] ?? 'Recorrente';
    }
}
