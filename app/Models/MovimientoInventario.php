<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventario';

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    const TIPO_ENTRADA = 'entrada';
    const TIPO_SALIDA = 'salida';
    const TIPO_TRASLADO = 'traslado';
    const TIPO_AJUSTE = 'ajuste';

    protected $fillable = [
        'item_id',
        'tipo',
        'cantidad',
        'area_origen_id',
        'area_destino_id',
        'usuario_id',
        'motivo',
        'observacion',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'tipo' => 'string',
        'created_at' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function areaOrigen(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_origen_id');
    }

    public function areaDestino(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_destino_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
