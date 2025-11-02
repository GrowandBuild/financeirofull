<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function apiIndex()
    {
        $purchases = Purchase::with('product')
            ->where('user_id', Auth::id())
            ->orderBy('purchase_date', 'desc')
            ->get();
        
        return response()->json($purchases);
    }
    
    public function apiStore(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0.01',
            'store' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);
        
        $purchase = Purchase::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'total_value' => $request->price * $request->quantity,
            'store' => $request->store,
            'purchase_date' => $request->purchase_date,
            'notes' => $request->notes
        ]);
        
        return response()->json($purchase, 201);
    }
    
    public function apiUpdate(Request $request, Purchase $purchase)
    {
        // Verificar se o usuário pode editar esta compra
        if ($purchase->user_id !== Auth::id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }
        
        $request->validate([
            'price' => 'sometimes|required|numeric|min:0',
            'quantity' => 'sometimes|required|numeric|min:0.01',
            'store' => 'nullable|string|max:255',
            'purchase_date' => 'sometimes|required|date',
            'notes' => 'nullable|string'
        ]);
        
        $data = $request->all();
        
        // Recalcular total se preço ou quantidade mudaram
        if (isset($data['price']) || isset($data['quantity'])) {
            $price = $data['price'] ?? $purchase->price;
            $quantity = $data['quantity'] ?? $purchase->quantity;
            $data['total_value'] = $price * $quantity;
        }
        
        $purchase->update($data);
        
        return response()->json($purchase);
    }
    
    public function apiDestroy(Purchase $purchase)
    {
        // Verificar se o usuário pode deletar esta compra
        if ($purchase->user_id !== Auth::id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }
        
        $purchase->delete();
        
        return response()->json(['message' => 'Compra deletada com sucesso']);
    }
}