<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'empresa_id',
        'estado',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function areasAsignadas(): HasMany
    {
        return $this->hasMany(Area::class, 'encargado_id');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'usuario_id');
    }

    public function esSuperAdmin(): bool
    {
        return $this->hasRole('super administrador');
    }

    public function esAdministradorEmpresa(): bool
    {
        return $this->hasRole('administrador de empresa');
    }

    public function esEncargadoArea(): bool
    {
        return $this->hasRole('encargado de area');
    }

    public function esConsulta(): bool
    {
        return $this->hasRole('consulta');
    }

    public function areasDisponibles()
    {
        if ($this->esSuperAdmin()) {
            return Area::query();
        }

        if ($this->esEncargadoArea()) {
            return Area::where('encargado_id', $this->id);
        }

        return Area::whereHas('sucursal', function ($query) {
            $query->where('empresa_id', $this->empresa_id);
        });
    }
}
