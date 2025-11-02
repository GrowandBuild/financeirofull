<?php

namespace App\Http\Controllers;

use App\Models\CashFlow;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CashFlowController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Dados do mês atual
        $currentMonth = Carbon::now();
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();
        
        // Receitas e despesas do mês
        $monthlyIncome = CashFlow::where('user_id', $user->id)
            ->income()
            ->confirmed()
            ->byDateRange($startOfMonth, $endOfMonth)
            ->sum('amount');
            
        $monthlyExpense = CashFlow::where('user_id', $user->id)
            ->expense()
            ->confirmed()
            ->byDateRange($startOfMonth, $endOfMonth)
            ->sum('amount');
        
        $monthlyBalance = $monthlyIncome - $monthlyExpense;
        
        // Transações recentes
        $recentTransactions = CashFlow::where('user_id', $user->id)
            ->with('category')
            ->orderBy('transaction_date', 'desc')
            ->limit(5)
            ->get();
        
        // Categorias mais usadas
        $topCategories = Category::where('user_id', $user->id)
            ->withCount('cashFlows')
            ->orderBy('cash_flows_count', 'desc')
            ->limit(5)
            ->get();
        
        // Dados para gráfico (últimos 6 meses)
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $currentMonth->copy()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $income = CashFlow::where('user_id', $user->id)
                ->income()
                ->confirmed()
                ->byDateRange($monthStart, $monthEnd)
                ->sum('amount');
                
            $expense = CashFlow::where('user_id', $user->id)
                ->expense()
                ->confirmed()
                ->byDateRange($monthStart, $monthEnd)
                ->sum('amount');
            
            $chartData[] = [
                'month' => $month->format('M/Y'),
                'income' => $income,
                'expense' => $expense,
                'balance' => $income - $expense
            ];
        }
        
        return view('cashflow.dashboard', compact(
            'monthlyIncome',
            'monthlyExpense', 
            'monthlyBalance',
            'recentTransactions',
            'topCategories',
            'chartData'
        ));
    }
    
    public function transactions(Request $request)
    {
        $user = Auth::user();
        $query = CashFlow::where('user_id', $user->id)->with('category');
        
        // Filtros
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        
        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20);
        $categories = Category::where('user_id', $user->id)->active()->get();
        
        return view('cashflow.transactions', compact('transactions', 'categories'));
    }
    
    public function add()
    {
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)->active()->get();
        
        return view('cashflow.add', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'category_id' => 'nullable|exists:categories,id',
            'goal_category' => 'nullable|in:fixed_expenses,professional_resources,emergency_reserves,leisure,debt_installments',
            'transaction_date' => 'required|date',
            'payment_method' => 'nullable|in:cash,card,pix,transfer',
            'reference' => 'nullable|string|max:255',
            'is_recurring' => 'boolean',
            'is_confirmed' => 'boolean'
        ]);
        
        $data = $request->all();
        $data['user_id'] = Auth::id();
        
        CashFlow::create($data);
        
        return redirect()->route('cashflow.dashboard')
            ->with('success', 'Transação adicionada com sucesso!');
    }
    
    public function reports()
    {
        $user = Auth::user();
        
        // Dados para relatórios
        $currentYear = Carbon::now()->year;
        $yearlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($currentYear, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($currentYear, $month, 1)->endOfMonth();
            
            $income = CashFlow::where('user_id', $user->id)
                ->income()
                ->confirmed()
                ->byDateRange($monthStart, $monthEnd)
                ->sum('amount');
                
            $expense = CashFlow::where('user_id', $user->id)
                ->expense()
                ->confirmed()
                ->byDateRange($monthStart, $monthEnd)
                ->sum('amount');
            
            $yearlyData[] = [
                'month' => $monthStart->format('M'),
                'income' => $income,
                'expense' => $expense,
                'balance' => $income - $expense
            ];
        }
        
        // Categorias com mais gastos
        $categoryExpenses = Category::where('user_id', $user->id)
            ->expense()
            ->withSum('cashFlows', 'amount')
            ->orderBy('cash_flows_sum_amount', 'desc')
            ->get();
        
        return view('cashflow.reports', compact('yearlyData', 'categoryExpenses'));
    }
}
