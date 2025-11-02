<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('purchases')->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        
        // Processar upload de imagem
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('products', 'public');
            $data['image_path'] = $imagePath;
        }
        
        // Processar variantes se existirem
        if ($request->has('variants')) {
            $data['variants'] = $request->variants;
            $data['has_variants'] = true;
        }

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $purchases = $product->purchases()->orderBy('purchase_date', 'desc')->get();
        return view('admin.products.show', compact('product', 'purchases'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();
        
        // Processar upload de imagem
        if ($request->hasFile('image_file')) {
            // Deletar imagem antiga se existir
            if ($product->image_path) {
                \Storage::disk('public')->delete($product->image_path);
            }
            
            $imagePath = $request->file('image_file')->store('products', 'public');
            $data['image_path'] = $imagePath;
            // Limpar URL quando usar upload
            $data['image'] = null;
        } else {
            // Se não há upload, manter apenas URL e limpar image_path
            if ($request->filled('image')) {
                // Deletar imagem antiga se existir
                if ($product->image_path) {
                    \Storage::disk('public')->delete($product->image_path);
                }
                $data['image_path'] = null;
            }
        }
        
        // Processar variantes se existirem
        if ($request->has('variants')) {
            $data['variants'] = $request->variants;
            $data['has_variants'] = true;
        } else {
            $data['variants'] = null;
            $data['has_variants'] = false;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produto excluído com sucesso!');
    }
}
