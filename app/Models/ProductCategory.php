<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductCategory extends Model
{
    protected $fillable = [
        'name',
        'normalized_name',
        'slug',
        'aliases',
        'usage_count',
        'is_active'
    ];

    protected $casts = [
        'aliases' => 'array',
        'is_active' => 'boolean',
        'usage_count' => 'integer'
    ];

    /**
     * Normaliza uma string de categoria para busca
     * Remove acentos, converte para minúsculas, remove espaços extras
     */
    public static function normalizeCategoryName(string $name): string
    {
        // Remove espaços extras e trim
        $normalized = trim($name);
        
        // Converte para minúsculas
        $normalized = mb_strtolower($normalized, 'UTF-8');
        
        // Remove acentos
        $normalized = self::removeAccents($normalized);
        
        // Remove caracteres especiais, mantém apenas letras, números e espaços
        $normalized = preg_replace('/[^a-z0-9\s]/', '', $normalized);
        
        // Remove espaços extras
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        
        // Remove espaços no início e fim
        $normalized = trim($normalized);
        
        return $normalized;
    }

    /**
     * Remove acentos de uma string
     */
    protected static function removeAccents(string $string): string
    {
        $accents = [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
            'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ç' => 'C', 'Ñ' => 'N'
        ];
        
        return strtr($string, $accents);
    }

    /**
     * Busca ou cria uma categoria normalizada
     */
    public static function findOrCreate(string $categoryName): self
    {
        $normalized = self::normalizeCategoryName($categoryName);
        
        // Primeiro tenta encontrar pela versão normalizada exata
        $category = self::where('normalized_name', $normalized)->first();
        
        if ($category) {
            // Se o nome original não é exatamente igual, atualiza para o nome mais usado
            if ($category->name !== $categoryName && $category->usage_count > 0) {
                // Mantém o nome original se já tem muitos produtos
                // Mas adiciona como alias se for diferente
                $aliases = $category->aliases ?? [];
                if (!in_array($normalized, $aliases) && $categoryName !== $category->name) {
                    $aliases[] = $categoryName;
                    $category->update(['aliases' => $aliases]);
                }
            }
            return $category;
        }
        
        // Busca por similaridade (plural/singular, variações)
        $allCategories = self::where('is_active', true)->get();
        foreach ($allCategories as $existingCategory) {
            // Verifica se são variações da mesma palavra
            if (self::areVariations($normalized, $existingCategory->normalized_name)) {
                // Atualiza aliases
                $aliases = $existingCategory->aliases ?? [];
                if (!in_array($normalized, $aliases)) {
                    $aliases[] = $normalized;
                }
                if (!in_array($categoryName, $aliases) && $categoryName !== $existingCategory->name) {
                    $aliases[] = $categoryName;
                }
                $existingCategory->update(['aliases' => $aliases]);
                return $existingCategory;
            }
        }
        
        // Tenta encontrar por aliases (variações)
        $category = self::where('is_active', true)
            ->get()
            ->filter(function ($cat) use ($normalized) {
                $aliases = $cat->aliases ?? [];
                return in_array($normalized, $aliases);
            })
            ->first();
        
        if ($category) {
            // Adiciona a variação aos aliases se não existir
            $aliases = $category->aliases ?? [];
            if (!in_array($normalized, $aliases)) {
                $aliases[] = $normalized;
            }
            if (!in_array($categoryName, $aliases) && $categoryName !== $category->name) {
                $aliases[] = $categoryName;
            }
            $category->update(['aliases' => $aliases]);
            return $category;
        }
        
        // Cria nova categoria
        return self::create([
            'name' => $categoryName,
            'normalized_name' => $normalized,
            'slug' => Str::slug($categoryName),
            'aliases' => [],
            'usage_count' => 0,
            'is_active' => true
        ]);
    }
    
    /**
     * Verifica se duas strings normalizadas são variações da mesma palavra
     * (ex: "vicio" e "vicios", "laticinio" e "laticinios")
     */
    protected static function areVariations(string $normalized1, string $normalized2): bool
    {
        // Se são iguais, não são variações
        if ($normalized1 === $normalized2) {
            return false;
        }
        
        // Remove espaços extras
        $str1 = trim($normalized1);
        $str2 = trim($normalized2);
        
        // Verifica se uma é sufixo da outra (plural/singular)
        if (strlen($str1) > strlen($str2)) {
            // Verifica se str2 + "s" ou "es" = str1
            if ($str1 === $str2 . 's' || $str1 === $str2 . 'es') {
                return true;
            }
        } else if (strlen($str2) > strlen($str1)) {
            // Verifica se str1 + "s" ou "es" = str2
            if ($str2 === $str1 . 's' || $str2 === $str1 . 'es') {
                return true;
            }
        }
        
        // Verifica similaridade (palavras muito parecidas)
        $similarity = similar_text($str1, $str2, $percent);
        if ($percent > 85 && abs(strlen($str1) - strlen($str2)) <= 2) {
            return true;
        }
        
        return false;
    }

    /**
     * Busca categorias similares (para autocomplete)
     */
    public static function searchSimilar(string $query, int $limit = 10): array
    {
        $normalized = self::normalizeCategoryName($query);
        
        // Busca por nome normalizado
        $categories = self::where('normalized_name', 'like', "%{$normalized}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->where('is_active', true)
            ->orderBy('usage_count', 'desc')
            ->orderBy('name', 'asc')
            ->limit($limit)
            ->get();
        
        // Busca por aliases
        $byAliases = self::where('is_active', true)
            ->get()
            ->filter(function ($category) use ($normalized) {
                $aliases = $category->aliases ?? [];
                foreach ($aliases as $alias) {
                    if (str_contains($alias, $normalized)) {
                        return true;
                    }
                }
                return false;
            })
            ->take($limit);
        
        // Combina resultados e remove duplicatas
        $allCategories = $categories->merge($byAliases)->unique('id');
        
        return $allCategories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'normalized_name' => $category->normalized_name,
                'usage_count' => $category->usage_count
            ];
        })->values()->toArray();
    }

    /**
     * Incrementa contador de uso
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Decrementa contador de uso
     */
    public function decrementUsage(): void
    {
        if ($this->usage_count > 0) {
            $this->decrement('usage_count');
        }
    }

    /**
     * Relacionamento com produtos
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category', 'name');
    }
}
