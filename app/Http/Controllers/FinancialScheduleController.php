<?php

namespace App\Http\Controllers;

use App\Models\FinancialSchedule;
use App\Models\CashFlow;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FinancialScheduleController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Mês atual ou especificado
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        
        // Buscar itens do mês separados por tipo, ordenados por valor (maior primeiro)
        $incomes = FinancialSchedule::where('user_id', $user->id)
            ->where('type', 'income')
            ->byMonth($month, $year)
            ->active() // Apenas não cancelados
            ->with('category')
            ->orderBy('amount', 'desc')
            ->get();
        
        $expenses = FinancialSchedule::where('user_id', $user->id)
            ->where('type', 'expense')
            ->byMonth($month, $year)
            ->active() // Apenas não cancelados
            ->with('category')
            ->orderBy('amount', 'desc')
            ->get();
        
        // Contar notificações (vencimentos próximos)
        $notificationCount = $this->getNotificationCount($user->id);
        
        return view('financial-schedule.index', compact('incomes', 'expenses', 'month', 'year', 'notificationCount'));
    }
    
    public function create()
    {
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('financial-schedule.create', compact('categories'));
    }
    
    public function edit($id)
    {
        $user = Auth::user();
        $schedule = FinancialSchedule::where('user_id', $user->id)
            ->findOrFail($id);
        
        $categories = Category::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('financial-schedule.edit', compact('schedule', 'categories'));
    }
    
    public function update(Request $request, $id)
    {
        $schedule = FinancialSchedule::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $request->validate([
            'type' => 'required|in:income,expense',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'category_id' => 'nullable|exists:categories,id',
            'goal_category' => 'nullable|in:fixed_expenses,professional_resources,emergency_reserves,leisure,debt_installments',
            'scheduled_date' => 'nullable|date',
            'scheduled_day' => 'nullable|integer|min:1|max:31',
            'image' => 'nullable|image|max:2048',
            'recurring_frequency' => 'nullable|in:daily,weekly,biweekly,monthly,quarterly,semiannual,yearly'
        ]);
        
        // Validar que pelo menos uma das datas foi preenchida
        if (!$request->filled('scheduled_date') && !$request->filled('scheduled_day')) {
            return back()->withErrors(['scheduled_date' => 'Você deve preencher uma das datas.'])->withInput();
        }
        
        $data = $request->all();
        
        // Determinar se é recorrente baseado no campo preenchido
        $isRecurring = $request->has('scheduled_day') && $request->filled('scheduled_day');
        
        if ($isRecurring) {
            $data['is_recurring'] = true;
            $data['recurring_config'] = [
                'frequency' => $request->input('recurring_frequency', 'monthly')
            ];
            
            // Se for recorrente e veio scheduled_day, converter para data completa
            $day = (int) $request->input('scheduled_day');
            $now = now();
            if ($now->day > $day) {
                $scheduledDate = $now->copy()->addMonth()->day($day);
            } else {
                $scheduledDate = $now->copy()->day($day);
            }
            $data['scheduled_date'] = $scheduledDate->format('Y-m-d');
        } else {
            $data['is_recurring'] = false;
            $data['recurring_config'] = null;
        }
        
        // Remover scheduled_day se existir
        unset($data['scheduled_day']);
        
        // Upload de nova imagem se houver
        if ($request->hasFile('image')) {
            // Deletar imagem antiga se existir
            if ($schedule->image_path && file_exists(storage_path('app/public/' . $schedule->image_path))) {
                unlink(storage_path('app/public/' . $schedule->image_path));
            }
            $imagePath = $request->file('image')->store('financial-schedules', 'public');
            $data['image_path'] = $imagePath;
        } else {
            // Manter a imagem atual se não houver nova
            unset($data['image']);
        }
        
        $schedule->update($data);
        
        return redirect()->route('financial-schedule.index')
            ->with('success', 'Item agendado atualizado com sucesso!');
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
            'scheduled_date' => 'nullable|date',
            'scheduled_day' => 'nullable|integer|min:1|max:31',
            'image' => 'nullable|image|max:2048',
            'recurring_frequency' => 'nullable|in:daily,weekly,biweekly,monthly,quarterly,semiannual,yearly'
        ]);
        
        // Validar que pelo menos uma das datas foi preenchida
        if (!$request->filled('scheduled_date') && !$request->filled('scheduled_day')) {
            return back()->withErrors(['scheduled_date' => 'Você deve preencher uma das datas.'])->withInput();
        }
        
        $user = Auth::user();
        $data = $request->all();
        $data['user_id'] = $user->id;
        
        // Determinar se é recorrente baseado no campo preenchido
        // Se tem scheduled_day, é recorrente. Se tem scheduled_date, é único
        $isRecurring = $request->has('scheduled_day') && $request->filled('scheduled_day');
        
        if ($isRecurring) {
            $data['is_recurring'] = true;
            $data['recurring_config'] = [
                'frequency' => $request->input('recurring_frequency', 'monthly')
            ];
            
            // Se for recorrente e veio scheduled_day, converter para data completa
            $day = (int) $request->input('scheduled_day');
            // Usar o primeiro dia disponível deste mês ou próximo mês
            $now = now();
            if ($now->day > $day) {
                // Se o dia já passou, usar próximo mês
                $scheduledDate = $now->copy()->addMonth()->day($day);
            } else {
                // Senão, usar este mês
                $scheduledDate = $now->copy()->day($day);
            }
            $data['scheduled_date'] = $scheduledDate->format('Y-m-d');
        } else {
            $data['is_recurring'] = false;
            $data['recurring_config'] = null;
        }
        
        // Remover scheduled_day se existir
        unset($data['scheduled_day']);
        
        // Upload de imagem se houver
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('financial-schedules', 'public');
            $data['image_path'] = $imagePath;
        }
        
        FinancialSchedule::create($data);
        
        return redirect()->route('financial-schedule.index')
            ->with('success', 'Item agendado criado com sucesso!');
    }
    
    public function confirm($id)
    {
        $schedule = FinancialSchedule::where('user_id', Auth::id())
            ->findOrFail($id);
        
        if ($schedule->is_confirmed) {
            return redirect()->back()->with('error', 'Este item já foi confirmado.');
        }
        
        // Criar transação no Fluxo de Caixa
        CashFlow::create([
            'user_id' => $schedule->user_id,
            'type' => $schedule->type,
            'title' => $schedule->title,
            'description' => $schedule->description,
            'amount' => $schedule->amount,
            'category_id' => $schedule->category_id,
            'goal_category' => $schedule->goal_category ?? null,
            'transaction_date' => now(),
            'payment_method' => 'transfer', // Padrão
            'reference' => 'schedule_' . $schedule->id, // ID do agendamento
            'is_confirmed' => true
        ]);
        
        // Marcar como confirmado
        $schedule->update([
            'is_confirmed' => true,
            'confirmed_at' => now()
        ]);
        
        return redirect()->back()->with('success', 'Item confirmado e adicionado ao Fluxo de Caixa!');
    }
    
    public function unconfirm($id)
    {
        $schedule = FinancialSchedule::where('user_id', Auth::id())
            ->findOrFail($id);
        
        if (!$schedule->is_confirmed) {
            return redirect()->back()->with('error', 'Este item não está confirmado.');
        }
        
        // Remover a transação do Fluxo de Caixa criada pela confirmação
        CashFlow::where('user_id', Auth::id())
            ->where('reference', 'schedule_' . $schedule->id)
            ->delete();
        
        // Desfazer confirmação
        $schedule->update([
            'is_confirmed' => false,
            'confirmed_at' => null
        ]);
        
        return redirect()->back()->with('success', 'Confirmação desfeita e transação removida do Fluxo de Caixa!');
    }
    
    public function cancel(Request $request, $id)
    {
        $schedule = FinancialSchedule::where('user_id', Auth::id())
            ->findOrFail($id);
        
        if ($schedule->is_cancelled) {
            return redirect()->back()->with('error', 'Este item já foi cancelado.');
        }
        
        $reason = $request->input('cancellation_reason');
        $schedule->cancel($reason);
        
        return redirect()->back()->with('success', 'Agendamento cancelado com sucesso!');
    }
    
    public function destroy($id)
    {
        $schedule = FinancialSchedule::where('user_id', Auth::id())
            ->findOrFail($id);
        
        // Deletar imagem se houver
        if ($schedule->image_path && file_exists(storage_path('app/public/' . $schedule->image_path))) {
            unlink(storage_path('app/public/' . $schedule->image_path));
        }
        
        $schedule->delete();
        
        return redirect()->back()->with('success', 'Item excluído com sucesso!');
    }
    
    private function getNotificationCount($userId, $days = 7): int
    {
        return FinancialSchedule::where('user_id', $userId)
            ->where('is_confirmed', false)
            ->where('is_cancelled', false)
            ->whereBetween('scheduled_date', [now()->startOfDay(), now()->addDays($days)])
            ->count();
    }
    
    public function getNotifications()
    {
        $user = Auth::user();
        $count = $this->getNotificationCount($user->id);
        
        return response()->json(['count' => $count]);
    }
    
    // ========== API METHODS ==========
    
    public function apiIndex()
    {
        $schedules = FinancialSchedule::where('user_id', Auth::id())
            ->with('category')
            ->orderBy('scheduled_date', 'desc')
            ->get();
        
        return response()->json($schedules);
    }
    
    public function apiStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'category_id' => 'nullable|exists:categories,id',
            'goal_category' => 'nullable|in:fixed_expenses,professional_resources,emergency_reserves,leisure,debt_installments',
            'scheduled_date' => 'nullable|date',
            'scheduled_day' => 'nullable|integer|min:1|max:31',
            'recurring_frequency' => 'nullable|in:daily,weekly,biweekly,monthly,quarterly,semiannual,yearly'
        ]);
        
        $data = $request->all();
        $data['user_id'] = Auth::id();
        
        // Determinar se é recorrente
        $isRecurring = $request->has('scheduled_day') && $request->filled('scheduled_day');
        if ($isRecurring) {
            $data['is_recurring'] = true;
            $data['recurring_frequency'] = $request->recurring_frequency ?? 'monthly';
            // Calcular próxima data baseado no dia
            $day = (int) $request->scheduled_day;
            $now = now();
            if ($day <= $now->day) {
                $data['scheduled_date'] = $now->copy()->addMonth()->day($day)->format('Y-m-d');
            } else {
                $data['scheduled_date'] = $now->copy()->day($day)->format('Y-m-d');
            }
        } else {
            $data['is_recurring'] = false;
            $data['scheduled_date'] = $request->scheduled_date ?? now()->format('Y-m-d');
        }
        
        $schedule = FinancialSchedule::create($data);
        
        return response()->json($schedule->load('category'), 201);
    }
    
    public function apiUpdate(Request $request, FinancialSchedule $schedule)
    {
        // Verificar se o usuário pode editar esta agenda
        if ($schedule->user_id !== Auth::id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }
        
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'sometimes|numeric|min:0.01',
            'category_id' => 'nullable|exists:categories,id',
            'goal_category' => 'nullable|in:fixed_expenses,professional_resources,emergency_reserves,leisure,debt_installments',
            'scheduled_date' => 'sometimes|date',
            'scheduled_day' => 'nullable|integer|min:1|max:31',
            'recurring_frequency' => 'nullable|in:daily,weekly,biweekly,monthly,quarterly,semiannual,yearly'
        ]);
        
        $schedule->update($request->all());
        
        return response()->json($schedule->load('category'));
    }
    
    public function apiDestroy(FinancialSchedule $schedule)
    {
        // Verificar se o usuário pode deletar esta agenda
        if ($schedule->user_id !== Auth::id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }
        
        // Deletar imagem se houver
        if ($schedule->image_path && file_exists(storage_path('app/public/' . $schedule->image_path))) {
            unlink(storage_path('app/public/' . $schedule->image_path));
        }
        
        $schedule->delete();
        
        return response()->json(['message' => 'Agenda deletada com sucesso']);
    }
}
