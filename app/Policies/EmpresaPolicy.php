<?php

namespace App\Policies;

use App\Models\Empresa;
use App\Models\User;

class EmpresaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('ver empresas');
    }

    public function view(User $user, Empresa $empresa): bool
    {
        return $user->hasPermissionTo('ver empresas');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('crear empresas');
    }

    public function update(User $user, Empresa $empresa): bool
    {
        return $user->hasPermissionTo('editar empresas');
    }

    public function delete(User $user, Empresa $empresa): bool
    {
        return $user->hasPermissionTo('eliminar empresas');
    }
}