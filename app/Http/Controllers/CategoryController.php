<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function quickCreate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense'
        ]);
        
        $user = Auth::user();
        
        // Verificar se já existe
        $existing = Category::where('user_id', $user->id)
            ->where('name', $request->name)
            ->where('type', $request->type)
            ->first();
            
        if ($existing) {
            return response()->json([
                'success' => true,
                'category' => $existing,
                'message' => 'Categoria já existe'
            ]);
        }
        
        $category = Category::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'type' => $request->type,
            'color' => $request->input('color', '#3b82f6'),
            'is_active' => true
        ]);
        
        return response()->json([
            'success' => true,
            'category' => $category,
            'message' => 'Categoria criada com sucesso!'
        ]);
    }
}
