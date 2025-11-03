<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        return true; // Todos os usuários autenticados podem ver produtos
    }
    
    /**
     * Determine if the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Todos os usuários autenticados podem criar produtos
    }
    
    /**
     * Determine if the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        return true; // Todos os usuários autenticados podem atualizar produtos
    }
    
    /**
     * Determine if the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        return true; // Todos os usuários autenticados podem deletar produtos
    }
}

