<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sucursal_id',
        'encargado_id',
        'nombre',
        'descripcion',
        'estado',
    ];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function encargado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encargado_id');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'inventario_area')
            ->withPivot('cantidad')
            ->withTimestamps();
    }

    public function empresa()
    {
        return $this->sucursal->empresa();
    }
}
