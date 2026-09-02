<?php

namespace App\Policies;

use App\Models\Sucursal;
use App\Models\User;

class SucursalPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('ver sucursales');
    }

    public function view(User $user, Sucursal $sucursal): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('ver sucursales')
            && $sucursal->empresa_id === $user->empresa_id;
    }

    public function create(User $user): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('crear sucursales');
    }

    public function update(User $user, Sucursal $sucursal): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('editar sucursales')
            && $sucursal->empresa_id === $user->empresa_id;
    }

    public function delete(User $user, Sucursal $sucursal): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('eliminar sucursales')
            && $sucursal->empresa_id === $user->empresa_id;
    }
}