<?php

namespace App\Services;

use App\Models\Area;
use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\MovimientoInventario;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InventarioService
{
    public function stockEnArea(Item $item, Area $area): float
    {
        $registro = InventarioArea::where('item_id', $item->id)
            ->where('area_id', $area->id)
            ->first();

        return $registro ? (float) $registro->cantidad : 0.0;
    }

    public function entrada(Item $item, Area $area, float $cantidad, User $usuario, ?string $motivo = null, ?string $observacion = null): MovimientoInventario
    {
        if ($cantidad <= 0) {
            throw new \InvalidArgumentException('La cantidad de entrada debe ser mayor a cero.');
        }

        return DB::transaction(function () use ($item, $area, $cantidad, $usuario, $motivo, $observacion) {
            $this->incrementarStock($item, $area, $cantidad);

            return MovimientoInventario::create([
                'item_id' => $item->id,
                'tipo' => MovimientoInventario::TIPO_ENTRADA,
                'cantidad' => $cantidad,
                'area_destino_id' => $area->id,
                'usuario_id' => $usuario->id,
                'motivo' => $motivo,
                'observacion' => $observacion,
            ]);
        });
    }

    public function salida(Item $item, Area $area, float $cantidad, User $usuario, ?string $motivo = null, ?string $observacion = null): MovimientoInventario
    {
        if ($cantidad <= 0) {
            throw new \InvalidArgumentException('La cantidad de salida debe ser mayor a cero.');
        }

        return DB::transaction(function () use ($item, $area, $cantidad, $usuario, $motivo, $observacion) {
            $this->verificarStockSuficiente($item, $area, $cantidad);
            $this->decrementarStock($item, $area, $cantidad);

            return MovimientoInventario::create([
                'item_id' => $item->id,
                'tipo' => MovimientoInventario::TIPO_SALIDA,
                'cantidad' => $cantidad,
                'area_origen_id' => $area->id,
                'usuario_id' => $usuario->id,
                'motivo' => $motivo,
                'observacion' => $observacion,
            ]);
        });
    }

    public function traslado(Item $item, Area $areaOrigen, Area $areaDestino, float $cantidad, User $usuario, ?string $motivo = null): MovimientoInventario
    {
        if ($cantidad <= 0) {
            throw new \InvalidArgumentException('La cantidad de traslado debe ser mayor a cero.');
        }

        if ($areaOrigen->id === $areaDestino->id) {
            throw new \InvalidArgumentException('El área de origen y destino deben ser distintas.');
        }

        if ($item->empresa_id !== $areaOrigen->sucursal->empresa_id
            || $item->empresa_id !== $areaDestino->sucursal->empresa_id) {
            throw new \InvalidArgumentException('El traslado debe realizarse dentro de la misma empresa.');
        }

        return DB::transaction(function () use ($item, $areaOrigen, $areaDestino, $cantidad, $usuario, $motivo) {
            $this->verificarStockSuficiente($item, $areaOrigen, $cantidad);
            $this->decrementarStock($item, $areaOrigen, $cantidad);
            $this->incrementarStock($item, $areaDestino, $cantidad);

            return MovimientoInventario::create([
                'item_id' => $item->id,
                'tipo' => MovimientoInventario::TIPO_TRASLADO,
                'cantidad' => $cantidad,
                'area_origen_id' => $areaOrigen->id,
                'area_destino_id' => $areaDestino->id,
                'usuario_id' => $usuario->id,
                'motivo' => $motivo,
            ]);
        });
    }

    public function ajuste(Item $item, Area $area, float $cantidad, User $usuario, string $motivo, ?string $observacion = null): MovimientoInventario
    {
        if ($cantidad == 0) {
            throw new \InvalidArgumentException('La cantidad del ajuste no puede ser cero.');
        }

        if (empty(trim($motivo))) {
            throw new \InvalidArgumentException('El motivo del ajuste es obligatorio.');
        }

        return DB::transaction(function () use ($item, $area, $cantidad, $usuario, $motivo, $observacion) {
            if ($cantidad < 0) {
                $this->verificarStockSuficiente($item, $area, abs($cantidad));
                $this->decrementarStock($item, $area, abs($cantidad));
            } else {
                $this->incrementarStock($item, $area, $cantidad);
            }

            return MovimientoInventario::create([
                'item_id' => $item->id,
                'tipo' => MovimientoInventario::TIPO_AJUSTE,
                'cantidad' => $cantidad,
                'area_destino_id' => $area->id,
                'usuario_id' => $usuario->id,
                'motivo' => $motivo,
                'observacion' => $observacion,
            ]);
        });
    }

    private function verificarStockSuficiente(Item $item, Area $area, float $cantidad): void
    {
        $disponible = $this->stockEnArea($item, $area);

        if ($disponible < $cantidad) {
            throw new \RuntimeException(
                "Stock insuficiente. Disponible: {$disponible} en '{$area->nombre}'."
            );
        }
    }

    private function incrementarStock(Item $item, Area $area, float $cantidad): void
    {
        $registro = InventarioArea::firstOrCreate(
            ['item_id' => $item->id, 'area_id' => $area->id],
            ['cantidad' => 0]
        );

        $registro->increment('cantidad', $cantidad);
    }

    private function decrementarStock(Item $item, Area $area, float $cantidad): void
    {
        $registro = InventarioArea::where('item_id', $item->id)
            ->where('area_id', $area->id)
            ->first();

        if (!$registro) {
            throw new \RuntimeException('No existe stock de este ítem en el área indicada.');
        }

        $registro->decrement('cantidad', $cantidad);
    }
}