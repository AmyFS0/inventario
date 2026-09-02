<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'empresa_id',
        'categoria_id',
        'unidad_medida_id',
        'proveedor_id',
        'nombre',
        'sku',
        'descripcion',
        'imagen',
        'costo_unitario',
        'stock_minimo',
        'estado',
    ];

    protected $casts = [
        'costo_unitario' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
        'estado' => 'string',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class, 'inventario_area')
            ->withPivot('cantidad')
            ->withTimestamps();
    }

    public function inventarioArea(): HasMany
    {
        return $this->hasMany(InventarioArea::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    public function stockTotal()
    {
        return (float) $this->inventarioArea()->sum('cantidad');
    }
}
