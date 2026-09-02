<?php

namespace App\Policies;

use App\Models\MovimientoInventario;
use App\Models\User;

class MovimientoInventarioPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('ver movimientos');
    }

    public function view(User $user, MovimientoInventario $movimiento): bool
    {
        if ($user->esSuperAdmin()) {
            return true;
        }

        if (!$user->hasPermissionTo('ver movimientos')) {
            return false;
        }

        $item = $movimiento->item;

        return $item !== null && $item->empresa_id === $user->empresa_id;
    }

    public function createEntrada(User $user): bool
    {
        return $user->hasPermissionTo('registrar entradas');
    }

    public function createSalida(User $user): bool
    {
        return $user->hasPermissionTo('registrar salidas');
    }

    public function createTraslado(User $user): bool
    {
        return $user->hasPermissionTo('registrar traslados');
    }

    public function createAjuste(User $user): bool
    {
        return $user->hasPermissionTo('registrar ajustes');
    }

    public function update(User $user, MovimientoInventario $movimiento): bool
    {
        return false;
    }

    public function delete(User $user, MovimientoInventario $movimiento): bool
    {
        return false;
    }

    public function restore(User $user, MovimientoInventario $movimiento): bool
    {
        return false;
    }

    public function forceDelete(User $user, MovimientoInventario $movimiento): bool
    {
        return false;
    }
}