<?php

namespace App\Policies;

use App\Models\Proveedor;
use App\Models\User;

class ProveedorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('ver proveedores');
    }

    public function view(User $user, Proveedor $proveedor): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('ver proveedores') && $proveedor->empresa_id === $user->empresa_id;
    }

    public function create(User $user): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('crear proveedores');
    }

    public function update(User $user, Proveedor $proveedor): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('editar proveedores') && $proveedor->empresa_id === $user->empresa_id;
    }

    public function delete(User $user, Proveedor $proveedor): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('eliminar proveedores') && $proveedor->empresa_id === $user->empresa_id;
    }
}