<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->foreignId('unidad_medida_id')->nullable()->constrained('unidades_medida')->nullOnDelete();
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->string('nombre');
            $table->string('sku')->index();
            $table->string('descripcion')->nullable();
            $table->string('imagen')->nullable();
            $table->decimal('costo_unitario', 12, 2)->default(0);
            $table->decimal('stock_minimo', 12, 2)->default(0);
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
            $table->softDeletes();

            $table->index('empresa_id');
            $table->index('categoria_id');
            $table->unique(['empresa_id', 'sku']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
