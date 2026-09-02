<?php

namespace App\Policies;

use App\Models\UnidadMedida;
use App\Models\User;

class UnidadMedidaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('ver unidades');
    }

    public function view(User $user, UnidadMedida $unidad): bool
    {
        return $user->hasPermissionTo('ver unidades');
    }

    public function create(User $user): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('crear unidades');
    }

    public function update(User $user, UnidadMedida $unidad): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('editar unidades');
    }

    public function delete(User $user, UnidadMedida $unidad): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('eliminar unidades');
    }
}