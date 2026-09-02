<?php

namespace App\Policies;

use App\Models\Categoria;
use App\Models\User;

class CategoriaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('ver categorias');
    }

    public function view(User $user, Categoria $categoria): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('ver categorias') && $categoria->empresa_id === $user->empresa_id;
    }

    public function create(User $user): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('crear categorias');
    }

    public function update(User $user, Categoria $categoria): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('editar categorias') && $categoria->empresa_id === $user->empresa_id;
    }

    public function delete(User $user, Categoria $categoria): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('eliminar categorias') && $categoria->empresa_id === $user->empresa_id;
    }
}