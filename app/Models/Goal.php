<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'total_income',
        'distribution',
        'category_mapping',
        'is_active',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'total_income' => 'decimal:2',
        'distribution' => 'array',
        'category_mapping' => 'array',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    // Relacionamentos
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhere(function($q) use ($startDate, $endDate) {
                        $q->where('end_date', '>=', $startDate)
                          ->whereNull('end_date');
                    });
    }

    // Accessors
    public function getFormattedTotalIncomeAttribute()
    {
        return 'R$ ' . number_format($this->total_income, 2, ',', '.');
    }

    // Calcular valores reais baseado no CashFlow
    public function getCurrentExpenses($startDate, $endDate = null)
    {
        $endDate = $endDate ?? now();
        
        return CashFlow::where('user_id', $this->user_id)
            ->expense()
            ->confirmed()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');
    }

    // Calcular progresso de cada categoria
    public function getProgressForCategory($categoryKey)
    {
        $percentage = $this->distribution[$categoryKey] ?? 0;
        $expectedAmount = ($this->total_income * $percentage) / 100;
        
        // TODO: Implementar cálculo real baseado nas categorias reais
        return [
            'percentage' => $percentage,
            'expected_amount' => $expectedAmount,
            'current_amount' => 0, // Será implementado
            'progress_percentage' => 0
        ];
    }

    // Verificar se objetivo está dentro do prazo
    public function isInPeriod($date = null)
    {
        $date = $date ?? now();
        return $date >= $this->start_date && 
               ($this->end_date === null || $date <= $this->end_date);
    }
}
