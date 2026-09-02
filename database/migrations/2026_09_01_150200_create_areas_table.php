<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->cascadeOnDelete();
            $table->foreignId('encargado_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
            $table->softDeletes();

            $table->index('sucursal_id');
            $table->index('encargado_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
