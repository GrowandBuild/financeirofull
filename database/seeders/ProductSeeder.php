<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Purchase;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Queijo Muçarela',
                'category' => 'Laticínios',
                'unit' => 'kg',
                'description' => 'Queijo muçarela fresco',
                'image' => '/alimentos/steaak.jpg',
            ],
            [
                'name' => 'Leite Integral',
                'category' => 'Laticínios',
                'unit' => 'L',
                'description' => 'Leite integral 1L',
                'image' => '/alimentos/steaak.jpg',
            ],
            [
                'name' => 'Carne de Boi',
                'category' => 'Carnes',
                'unit' => 'kg',
                'description' => 'Carne bovina',
                'image' => '/alimentos/steaak.jpg',
            ],
            [
                'name' => 'Ovos',
                'category' => 'Proteínas',
                'unit' => 'dz',
                'description' => 'Ovos de galinha',
                'image' => '/alimentos/ovo.jpg',
            ],
            [
                'name' => 'Açafrão',
                'category' => 'Temperos',
                'unit' => 'g',
                'description' => 'Açafrão em pó',
                'image' => '/alimentos/acafrão.jpg',
            ],
            [
                'name' => 'Cuscuz',
                'category' => 'Cereais',
                'unit' => 'kg',
                'description' => 'Cuscuz de milho',
                'image' => '/alimentos/cuscuz.jpg',
            ],
            [
                'name' => 'Colorau',
                'category' => 'Temperos',
                'unit' => 'g',
                'description' => 'Colorau em pó',
                'image' => '/alimentos/colorau.jpg',
            ],
            [
                'name' => 'Cominho',
                'category' => 'Temperos',
                'unit' => 'g',
                'description' => 'Cominho em grão',
                'image' => '/alimentos/cominho.jpg',
            ],
            [
                'name' => 'Folha de Louro',
                'category' => 'Temperos',
                'unit' => 'g',
                'description' => 'Folhas de louro secas',
                'image' => '/alimentos/folha de louro.webp',
            ],
            [
                'name' => 'Carne de Hambúrguer',
                'category' => 'Carnes',
                'unit' => 'kg',
                'description' => 'Carne moída para hambúrguer',
                'image' => '/alimentos/carne de hamburguer.png',
            ],
            [
                'name' => 'Paleta de Carne',
                'category' => 'Carnes',
                'unit' => 'kg',
                'description' => 'Paleta bovina',
                'image' => '/alimentos/paleta.jpg',
            ],
            [
                'name' => 'Acem',
                'category' => 'Carnes',
                'unit' => 'kg',
                'description' => 'Acem bovino',
                'image' => '/alimentos/acem.jpg',
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);

            // Criar algumas compras de exemplo para cada produto
            $purchases = [
                [
                    'purchase_date' => now()->subDays(rand(1, 30)),
                    'price' => rand(30, 80) + (rand(0, 99) / 100),
                    'quantity' => rand(1, 5) + (rand(0, 9) / 10),
                    'store' => ['Supermercado A', 'Atacadão B', 'Mercado Central'][rand(0, 2)],
                    'notes' => 'Compra de ' . $product->name,
                ],
                [
                    'purchase_date' => now()->subDays(rand(1, 30)),
                    'price' => rand(30, 80) + (rand(0, 99) / 100),
                    'quantity' => rand(1, 5) + (rand(0, 9) / 10),
                    'store' => ['Supermercado A', 'Atacadão B', 'Mercado Central'][rand(0, 2)],
                    'notes' => 'Compra de ' . $product->name,
                ],
                [
                    'purchase_date' => now()->subDays(rand(1, 30)),
                    'price' => rand(30, 80) + (rand(0, 99) / 100),
                    'quantity' => rand(1, 5) + (rand(0, 9) / 10),
                    'store' => ['Supermercado A', 'Atacadão B', 'Mercado Central'][rand(0, 2)],
                    'notes' => 'Compra de ' . $product->name,
                ],
            ];

            foreach ($purchases as $purchaseData) {
                $purchaseData['total_value'] = $purchaseData['price'] * $purchaseData['quantity'];
                $purchaseData['user_id'] = 1; // Por enquanto fixo
                
                Purchase::create([
                    'product_id' => $product->id,
                    ...$purchaseData,
                ]);
            }

            // Atualizar estatísticas do produto
            $product->update([
                'average_price' => $product->purchases()->avg('price'),
                'last_price' => $product->purchases()->latest('purchase_date')->first()?->price ?? 0,
                'total_spent' => $product->purchases()->sum('total_value'),
                'purchase_count' => $product->purchases()->count(),
            ]);
        }
    }
}
