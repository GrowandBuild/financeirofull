<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\CashFlow;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GoalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Obter mês e ano da requisição, ou usar o mês atual
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        // Validar mês e ano
        $month = max(1, min(12, (int)$month));
        $year = max(2020, min(2100, (int)$year));
        
        $selectedDate = Carbon::create($year, $month, 1);
        
        $goals = Goal::where('user_id', $user->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calcular dados para cada objetivo
        $goalsData = [];
        foreach ($goals as $goal) {
            $goalsData[] = $this->calculateGoalProgress($goal);
        }
        
        // Calcular dados do default (40/10/30/10/10) para exibição
        $defaultGoalData = $this->calculateDefaultGoalProgress($user->id, $selectedDate);
        
        // Obter meses disponíveis (com dados)
        $availableMonths = $this->getAvailableMonths($user->id);
        
        return view('goals.index', compact('goals', 'goalsData', 'defaultGoalData', 'selectedDate', 'availableMonths'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)
            ->expense()
            ->active()
            ->orderBy('name')
            ->get();
        
        return view('goals.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_income' => 'required|numeric|min:0.01',
            'distribution' => 'required|array',
            'category_mapping' => 'nullable|array',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);
        
        // Validar que a soma das porcentagens seja 100%
        $totalPercentage = array_sum($validated['distribution']);
        if (abs($totalPercentage - 100) > 0.01) {
            return back()->withErrors(['distribution' => 'A soma das porcentagens deve ser exatamente 100%.'])->withInput();
        }
        
        $user = Auth::user();
        $validated['user_id'] = $user->id;
        $validated['is_active'] = true;
        
        Goal::create($validated);
        
        return redirect()->route('goals.index')
            ->with('success', 'Objetivo criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $goal = Goal::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $goalData = $this->calculateGoalProgress($goal);
        
        return view('goals.show', compact('goal', 'goalData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $goal = Goal::where('user_id', Auth::id())
            ->findOrFail($id);
        
        return view('goals.edit', compact('goal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $goal = Goal::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_income' => 'required|numeric|min:0.01',
            'distribution' => 'required|array',
            'is_active' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);
        
        // Validar que a soma das porcentagens seja 100%
        $totalPercentage = array_sum($validated['distribution']);
        if (abs($totalPercentage - 100) > 0.01) {
            return back()->withErrors(['distribution' => 'A soma das porcentagens deve ser exatamente 100%.'])->withInput();
        }
        
        $goal->update($validated);
        
        return redirect()->route('goals.index')
            ->with('success', 'Objetivo atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $goal = Goal::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $goal->delete();
        
        return redirect()->back()->with('success', 'Objetivo excluído com sucesso!');
    }

    /**
     * Calcular progresso de um objetivo
     */
    private function calculateGoalProgress($goal)
    {
        $startDate = Carbon::parse($goal->start_date);
        $endDate = $goal->end_date ? Carbon::parse($goal->end_date) : now();
        
        // Buscar entradas reais do período
        $actualIncome = CashFlow::where('user_id', $goal->user_id)
            ->income()
            ->confirmed()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');
        
        // Buscar despesas reais do período por categorias mapeadas
        $actualExpenses = CashFlow::where('user_id', $goal->user_id)
            ->expense()
            ->confirmed()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');
        
        // Calcular valores esperados baseado na distribuição
        $distribution = $goal->distribution;
        $expectedValues = [];
        $actualValues = [];
        $progressData = [];
        
        // Buscar despesas reais por categoria mapeada
        $categoryMapping = $goal->category_mapping ?? [];
        
        // Calcular saldo total disponível (receitas - despesas gerais)
        $totalBalance = $actualIncome - $actualExpenses;
        
        foreach ($distribution as $key => $percentage) {
            $expectedAmount = ($goal->total_income * $percentage) / 100;
            $expectedValues[$key] = $expectedAmount;
            
            // Calcular valor real gasto nesta categoria usando goal_category direto
            $actualAmount = CashFlow::where('user_id', $goal->user_id)
                ->expense()
                ->confirmed()
                ->where('goal_category', $key)
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount');
            
            $actualValues[$key] = $actualAmount;
            
            // Calcular quanto está disponível para este departamento baseado no saldo e percentual
            $availableAmount = ($totalBalance * $percentage) / 100;
            
            // Calcular porcentagem de progresso
            $progressPercentage = $expectedAmount > 0 ? min(100, ($actualAmount / $expectedAmount) * 100) : 0;
            
            $progressData[$key] = [
                'label' => $this->getCategoryLabel($key),
                'percentage' => $percentage,
                'expected_amount' => $expectedAmount,
                'actual_amount' => $actualAmount,
                'available_amount' => $availableAmount,
                'progress_percentage' => $progressPercentage
            ];
        }
        
        return [
            'goal' => $goal,
            'expected_total_income' => $goal->total_income,
            'actual_total_income' => $actualIncome,
            'expected_total_expenses' => $goal->total_income,
            'actual_total_expenses' => $actualExpenses,
            'total_balance' => $totalBalance,
            'distribution' => $progressData,
            'overall_progress' => $actualIncome > 0 ? min(100, ($actualExpenses / $goal->total_income) * 100) : 0
        ];
    }

    /**
     * Calcular progresso do objetivo padrão (40/10/30/10/10)
     */
    private function calculateDefaultGoalProgress($userId, $selectedDate = null)
    {
        if ($selectedDate === null) {
            $selectedDate = now();
        }
        
        $startDate = $selectedDate->copy()->startOfMonth();
        $endDate = $selectedDate->copy()->endOfMonth();
        
        // Distribuição padrão fixa
        $defaultDistribution = [
            'fixed_expenses' => 40,
            'professional_resources' => 10,
            'emergency_reserves' => 30,
            'leisure' => 10,
            'debt_installments' => 10
        ];
        
        // Buscar entradas reais do mês atual
        $actualIncome = CashFlow::where('user_id', $userId)
            ->income()
            ->confirmed()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');
        
        // Buscar despesas reais do mês atual
        $actualExpenses = CashFlow::where('user_id', $userId)
            ->expense()
            ->confirmed()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');
        
        // Calcular saldo total disponível
        $totalBalance = $actualIncome - $actualExpenses;
        
        $progressData = [];
        
        foreach ($defaultDistribution as $key => $percentage) {
            // Calcular valor real gasto nesta categoria
            $actualAmount = CashFlow::where('user_id', $userId)
                ->expense()
                ->confirmed()
                ->where('goal_category', $key)
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount');
            
            // Valor disponível baseado no saldo e percentual
            $availableAmount = ($totalBalance * $percentage) / 100;
            
            // Valor que deveria ser gasto baseado na renda mensal total atual
            $expectedMonthlyAmount = ($actualIncome * $percentage) / 100;
            
            // Calcular restante baseado no orçamento ideal (Valor Esperado)
            $remainingAmount = $expectedMonthlyAmount - $actualAmount;
            
            $progressData[$key] = [
                'label' => $this->getCategoryLabel($key),
                'percentage' => $percentage,
                'actual_amount' => $actualAmount,
                'available_amount' => $availableAmount, // Mantido para referência, mas não usado no cálculo principal
                'expected_monthly_amount' => $expectedMonthlyAmount,
                'remaining_amount' => $remainingAmount // Novo: quanto resta do orçamento ideal
            ];
        }
        
        return [
            'total_income' => $actualIncome,
            'total_expenses' => $actualExpenses,
            'total_balance' => $totalBalance,
            'distribution' => $progressData,
            'month' => $startDate->month,
            'year' => $startDate->year
        ];
    }

    /**
     * Obter meses disponíveis com dados
     */
    private function getAvailableMonths($userId)
    {
        $monthNames = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
        ];
        
        $months = CashFlow::where('user_id', $userId)
            ->selectRaw('YEAR(transaction_date) as year, MONTH(transaction_date) as month')
            ->groupByRaw('YEAR(transaction_date), MONTH(transaction_date)')
            ->orderByRaw('YEAR(transaction_date) DESC, MONTH(transaction_date) DESC')
            ->get()
            ->map(function ($item) use ($monthNames) {
                return [
                    'year' => (int)$item->year,
                    'month' => (int)$item->month,
                    'label' => $monthNames[(int)$item->month] . '/' . $item->year
                ];
            });
        
        return $months;
    }

    /**
     * Retornar label humanizado para as categorias
     */
    private function getCategoryLabel($key)
    {
        $labels = [
            'fixed_expenses' => 'Despesas Fixas',
            'investments' => 'Investimentos',
            'professional_resources' => 'Recursos Profissionais',
            'emergency_reserves' => 'Reservas de Emergência',
            'long_term_savings' => 'Poupanças de Longo Prazo',
            'leisure' => 'Lazer',
            'debt_installments' => 'Parcelas de Dívidas',
            'education' => 'Educação',
            'health' => 'Saúde'
        ];
        
        return $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    /**
     * API: Buscar despesas do mês atual
     */
    public function getMonthlyExpenses(Request $request)
    {
        $user = Auth::user();
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        
        $expenses = CashFlow::where('user_id', $user->id)
            ->expense()
            ->confirmed()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->with('category')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'title' => $expense->title,
                    'description' => $expense->description,
                    'amount' => $expense->amount,
                    'transaction_date' => $expense->transaction_date->format('d/m/Y'),
                    'goal_category' => $expense->goal_category,
                    'goal_category_label' => $expense->goal_category ? $this->getCategoryLabel($expense->goal_category) : null,
                    'category_name' => $expense->category ? $expense->category->name : null,
                    'payment_method' => $expense->payment_method_label
                ];
            });
        
        return response()->json([
            'success' => true,
            'expenses' => $expenses,
            'total' => $expenses->sum('amount'),
            'month' => $month,
            'year' => $year
        ]);
    }

    /**
     * API: Atualizar departamento de uma despesa
     */
    public function updateExpenseDepartment(Request $request, $id)
    {
        $user = Auth::user();
        
        $expense = CashFlow::where('user_id', $user->id)
            ->where('id', $id)
            ->first();
        
        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'Despesa não encontrada'
            ], 404);
        }
        
        $validated = $request->validate([
            'goal_category' => 'nullable|string|in:fixed_expenses,investments,professional_resources,emergency_reserves,long_term_savings,leisure,debt_installments,education,health'
        ]);
        
        $expense->update(['goal_category' => $validated['goal_category'] ?? null]);
        
        return response()->json([
            'success' => true,
            'message' => 'Departamento atualizado com sucesso',
            'expense' => [
                'id' => $expense->id,
                'goal_category' => $expense->goal_category,
                'goal_category_label' => $expense->goal_category ? $this->getCategoryLabel($expense->goal_category) : null
            ]
        ]);
    }
}
