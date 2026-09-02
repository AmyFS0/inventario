<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->enum('tipo', ['entrada', 'salida', 'traslado', 'ajuste']);
            $table->decimal('cantidad', 12, 2);
            $table->foreignId('area_origen_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->foreignId('area_destino_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->string('motivo')->nullable();
            $table->string('observacion')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('item_id');
            $table->index('tipo');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
