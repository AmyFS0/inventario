<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('gestionar usuarios')
            || $user->esSuperAdmin();
    }

    public function view(User $user, User $target): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('gestionar usuarios')
            && $target->empresa_id === $user->empresa_id;
    }

    public function create(User $user): bool
    {
        return $user->esSuperAdmin()
            || $user->hasPermissionTo('gestionar usuarios');
    }

    public function update(User $user, User $target): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('gestionar usuarios')
            && $target->empresa_id === $user->empresa_id;
    }

    public function delete(User $user, User $target): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        return $user->hasPermissionTo('gestionar usuarios')
            && $target->empresa_id === $user->empresa_id;
    }
}