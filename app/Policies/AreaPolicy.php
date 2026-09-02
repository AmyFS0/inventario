<?php

namespace App\Policies;

use App\Models\Area;
use App\Models\User;

class AreaPolicy
{
    private function enMismaEmpresa(User $user, Area $area): bool
    {
        return $area->sucursal->empresa_id === $user->empresa_id;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('ver areas');
    }

    public function view(User $user, Area $area): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('ver areas') && $this->enMismaEmpresa($user, $area);
    }

    public function create(User $user): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('crear areas');
    }

    public function update(User $user, Area $area): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('editar areas') && $this->enMismaEmpresa($user, $area);
    }

    public function delete(User $user, Area $area): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('eliminar areas') && $this->enMismaEmpresa($user, $area);
    }
}