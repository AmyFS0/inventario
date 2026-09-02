<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('ver items');
    }

    public function view(User $user, Item $item): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('ver items') && $item->empresa_id === $user->empresa_id;
    }

    public function create(User $user): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('crear items');
    }

    public function update(User $user, Item $item): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('editar items') && $item->empresa_id === $user->empresa_id;
    }

    public function delete(User $user, Item $item): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('eliminar items') && $item->empresa_id === $user->empresa_id;
    }
}