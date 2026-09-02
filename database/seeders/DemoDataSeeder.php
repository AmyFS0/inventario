<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\MovimientoInventario;
use App\Models\Proveedor;
use App\Models\Sucursal;
use App\Models\UnidadMedida;
use App\Models\User;
use App\Services\InventarioService;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- Usuarios ----------
        if (!User::where('email', 'admin@demo.com')->exists()) {
            $superAdmin = User::create([
                'name' => 'Super Administrador',
                'email' => 'admin@demo.com',
                'password' => 'password',
                'email_verified_at' => now(),
                'empresa_id' => null,
                'estado' => 'activo',
            ]);
            $superAdmin->assignRole('super administrador');
        }

        // ---------- Empresa ----------
        $empresa = Empresa::firstOrCreate(
            ['identificacion_fiscal' => '08019012345678'],
            [
                'nombre' => 'Empresa Demo S.A.',
                'direccion' => 'Col. Palmira, Tegucigalpa',
                'telefono' => '+504 2222-1234',
                'correo' => 'info@empresademo.com',
                'estado' => 'activo',
            ]
        );

        // ---------- Usuarios de la empresa ----------
        $administrador = User::firstOrCreate(
            ['email' => 'gerente@empresademo.com'],
            [
                'name' => 'Carlos Gerente',
                'password' => 'password',
                'email_verified_at' => now(),
                'empresa_id' => $empresa->id,
                'estado' => 'activo',
            ]
        );
        $administrador->assignRole('administrador de empresa');

        $nombresEncargados = [
            'María Encargada',
            'Juan Recibidor',
            'Pedro Bodeguero',
            'Lucía Cocinera',
            'Roberto Despacho',
            'Ana Almacenista',
        ];

        $encargados = [];
        foreach ($nombresEncargados as $i => $nombre) {
            $email = 'encargado' . ($i + 1) . '@empresademo.com';
            $usuario = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $nombre,
                    'password' => 'password',
                    'email_verified_at' => now(),
                    'empresa_id' => $empresa->id,
                    'estado' => 'activo',
                ]
            );
            $usuario->assignRole('encargado de area');
            $encargados[] = $usuario;
        }

        $usuarioConsulta = User::firstOrCreate(
            ['email' => 'consulta@empresademo.com'],
            [
                'name' => 'Sofia Consulta',
                'password' => 'password',
                'email_verified_at' => now(),
                'empresa_id' => $empresa->id,
                'estado' => 'activo',
            ]
        );
        $usuarioConsulta->assignRole('consulta');

        // ---------- Sucursales ----------
        $sucursal1 = Sucursal::firstOrCreate(
            ['empresa_id' => $empresa->id, 'nombre' => 'Sucursal Central'],
            ['direccion' => 'Centro Comercial Central, Local 5', 'telefono' => '+504 2222-0001', 'estado' => 'activo']
        );

        $sucursal2 = Sucursal::firstOrCreate(
            ['empresa_id' => $empresa->id, 'nombre' => 'Sucursal Norte'],
            ['direccion' => 'Boulevard Morazán, Avenida Norte', 'telefono' => '+504 2222-0002', 'estado' => 'activo']
        );

        // ---------- Áreas ----------
        $areasData = [
            ['sucursal_id' => $sucursal1->id, 'encargado' => $encargados[0], 'nombre' => 'Bodega Principal', 'descripcion' => 'Almacenamiento principal de mercadería.'],
            ['sucursal_id' => $sucursal1->id, 'encargado' => $encargados[1], 'nombre' => 'Recepción', 'descripcion' => 'Recepción de mercadería entrante.'],
            ['sucursal_id' => $sucursal1->id, 'encargado' => $encargados[2], 'nombre' => 'Piso de Venta', 'descripcion' => 'Exhibición y venta al público.'],
            ['sucursal_id' => $sucursal2->id, 'encargado' => $encargados[3], 'nombre' => 'Bodega Norte', 'descripcion' => 'Almacenamiento de la sucursal norte.'],
            ['sucursal_id' => $sucursal2->id, 'encargado' => $encargados[4], 'nombre' => 'Despacho', 'descripcion' => 'Despacho de pedidos.'],
            ['sucursal_id' => $sucursal2->id, 'encargado' => $encargados[5], 'nombre' => 'Cocina', 'descripcion' => 'Área de preparación de alimentos.'],
        ];

        $areas = [];
        foreach ($areasData as $data) {
            $area = Area::firstOrCreate(
                ['sucursal_id' => $data['sucursal_id'], 'nombre' => $data['nombre']],
                [
                    'encargado_id' => $data['encargado']->id,
                    'descripcion' => $data['descripcion'],
                    'estado' => 'activo',
                ]
            );
            $areas[] = $area;
        }

        // ---------- Categorías ----------
        $categorias = [];
        foreach (['Bebidas', 'Granos Básicos', 'Lácteos', 'Limpieza', 'Conservas', 'Carnes Frías'] as $nombre) {
            $categorias[] = Categoria::firstOrCreate(
                ['empresa_id' => $empresa->id, 'nombre' => $nombre]
            );
        }

        // ---------- Proveedores ----------
        $proveedor = Proveedor::firstOrCreate(
            ['empresa_id' => $empresa->id, 'nombre' => 'Distribuidora Nacional'],
            ['contacto' => 'Ana López', 'telefono' => '+504 2233-4455', 'correo' => 'ventas@distnacional.com']
        );

        // ---------- Unidades ----------
        $unidad = UnidadMedida::where('nombre', 'Unidad')->first();
        $caja = UnidadMedida::where('nombre', 'Caja')->first();
        $kg = UnidadMedida::where('nombre', 'Kilogramo')->first();
        $litro = UnidadMedida::where('nombre', 'Litro')->first();

        // ---------- Items (16) ----------
        $itemsData = [
            ['nombre' => 'Agua Purificada 5L', 'categoria' => $categorias[0], 'unidad' => $litro, 'costo' => 45.00, 'minimo' => 20, 'proveedor' => $proveedor->id],
            ['nombre' => 'Refresco Cola 2L', 'categoria' => $categorias[0], 'unidad' => $unidad, 'costo' => 32.50, 'minimo' => 12, 'proveedor' => $proveedor->id],
            ['nombre' => 'Jugo de Naranja 1L', 'categoria' => $categorias[0], 'unidad' => $unidad, 'costo' => 28.00, 'minimo' => 15, 'proveedor' => null],
            ['nombre' => 'Arroz 1lb', 'categoria' => $categorias[1], 'unidad' => $kg, 'costo' => 18.00, 'minimo' => 50, 'proveedor' => $proveedor->id],
            ['nombre' => 'Frijoles 1lb', 'categoria' => $categorias[1], 'unidad' => $kg, 'costo' => 35.00, 'minimo' => 40, 'proveedor' => $proveedor->id],
            ['nombre' => 'Azúcar 1lb', 'categoria' => $categorias[1], 'unidad' => $kg, 'costo' => 12.50, 'minimo' => 30, 'proveedor' => null],
            ['nombre' => 'Sal 1lb', 'categoria' => $categorias[1], 'unidad' => $kg, 'costo' => 5.00, 'minimo' => 20, 'proveedor' => null],
            ['nombre' => 'Leche Entera 1L', 'categoria' => $categorias[2], 'unidad' => $litro, 'costo' => 52.00, 'minimo' => 15, 'proveedor' => $proveedor->id],
            ['nombre' => 'Queso Crema 250g', 'categoria' => $categorias[2], 'unidad' => $unidad, 'costo' => 48.00, 'minimo' => 10, 'proveedor' => null],
            ['nombre' => 'Mantequilla 225g', 'categoria' => $categorias[2], 'unidad' => $unidad, 'costo' => 55.00, 'minimo' => 10, 'proveedor' => null],
            ['nombre' => 'Detergente en Polvo 1kg', 'categoria' => $categorias[3], 'unidad' => $kg, 'costo' => 45.00, 'minimo' => 10, 'proveedor' => $proveedor->id],
            ['nombre' => 'Cloro 1L', 'categoria' => $categorias[3], 'unidad' => $litro, 'costo' => 22.00, 'minimo' => 12, 'proveedor' => null],
            ['nombre' => 'Jabón de Tocador', 'categoria' => $categorias[3], 'unidad' => $unidad, 'costo' => 15.00, 'minimo' => 24, 'proveedor' => null],
            ['nombre' => 'Atún en Lata', 'categoria' => $categorias[4], 'unidad' => $unidad, 'costo' => 27.00, 'minimo' => 18, 'proveedor' => $proveedor->id],
            ['nombre' => 'Sardina en Lata', 'categoria' => $categorias[4], 'unidad' => $unidad, 'costo' => 20.00, 'minimo' => 15, 'proveedor' => null],
            ['nombre' => 'Salami 400g', 'categoria' => $categorias[5], 'unidad' => $unidad, 'costo' => 85.00, 'minimo' => 8, 'proveedor' => $proveedor->id],
        ];

        $items = [];
        foreach ($itemsData as $i => $data) {
            $item = Item::firstOrCreate(
                ['empresa_id' => $empresa->id, 'sku' => 'SKU-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT)],
                [
                    'nombre' => $data['nombre'],
                    'categoria_id' => $data['categoria']->id,
                    'unidad_medida_id' => $data['unidad']->id,
                    'proveedor_id' => $data['proveedor'],
                    'costo_unitario' => $data['costo'],
                    'stock_minimo' => $data['minimo'],
                    'descripcion' => null,
                    'imagen' => null,
                    'estado' => 'activo',
                ]
            );
            $items[] = $item;
        }

        // ---------- Inventario base por área ----------
        $distribuciones = [
            0 => ['Agua Purificada 5L' => 60, 'Refresco Cola 2L' => 24, 'Arroz 1lb' => 120, 'Frijoles 1lb' => 80, 'Azúcar 1lb' => 60, 'Detergente en Polvo 1kg' => 20, 'Atún en Lata' => 30],
            1 => ['Agua Purificada 5L' => 20, 'Refresco Cola 2L' => 12, 'Jugo de Naranja 1L' => 18, 'Leche Entera 1L' => 25, 'Queso Crema 250g' => 12, 'Cloro 1L' => 10],
            2 => ['Refresco Cola 2L' => 6, 'Jugo de Naranja 1L' => 20, 'Sal 1lb' => 25, 'Azúcar 1lb' => 30, 'Jabón de Tocador' => 40, 'Atún en Lata' => 25],
            3 => ['Arroz 1lb' => 100, 'Frijoles 1lb' => 60, 'Azúcar 1lb' => 40, 'Sal 1lb' => 30, 'Detergente en Polvo 1kg' => 15, 'Mantequilla 225g' => 8, 'Sardina en Lata' => 20],
            4 => ['Agua Purificada 5L' => 30, 'Frijoles 1lb' => 25, 'Leche Entera 1L' => 15, 'Salami 400g' => 6, 'Mantequilla 225g' => 10, 'Queso Crema 250g' => 8],
            5 => ['Leche Entera 1L' => 8, 'Arroz 1lb' => 20, 'Sal 1lb' => 5, 'Salami 400g' => 3],
        ];

        $stockInicial = [];
        foreach ($areas as $idx => $area) {
            foreach ($distribuciones[$idx] as $nombre => $cantidad) {
                $item = collect($items)->firstWhere('nombre', $nombre);
                if (!$item) {
                    continue;
                }
                $stockInicial[] = [
                    'item_id' => $item->id,
                    'area_id' => $area->id,
                    'cantidad' => $cantidad,
                ];
            }
        }

        InventarioArea::insert($stockInicial);

        // ---------- Movimientos de ejemplo ----------
        if (MovimientoInventario::count() > 0) {
            return;
        }

        $service = app(InventarioService::class);

        $movimientos = [
            ['tipo' => 'entrada', 'item' => $items[0], 'areaOrigen' => null, 'areaDestino' => $areas[0], 'cantidad' => 60, 'usuario' => $administrador],
            ['tipo' => 'entrada', 'item' => $items[3], 'areaOrigen' => null, 'areaDestino' => $areas[0], 'cantidad' => 120, 'usuario' => $administrador],
            ['tipo' => 'traslado', 'item' => $items[0], 'areaOrigen' => $areas[0], 'areaDestino' => $areas[1], 'cantidad' => 20, 'usuario' => $administrador],
            ['tipo' => 'salida', 'item' => $items[3], 'areaOrigen' => $areas[0], 'areaDestino' => null, 'cantidad' => 10, 'usuario' => $encargados[0]],
            ['tipo' => 'ajuste', 'item' => $items[0], 'areaOrigen' => null, 'areaDestino' => $areas[0], 'cantidad' => -5, 'usuario' => $administrador],
        ];

        foreach ($movimientos as $mov) {
            $item = $mov['item'];
            $tipo = $mov['tipo'];
            $usuario = $mov['usuario'];
            $motivo = match ($tipo) {
                'entrada' => 'Compra a proveedor',
                'salida' => 'Consumo interno',
                'traslado' => 'Reabastecimiento de área',
                'ajuste' => 'Conteo físico: merma detectada',
            };

            try {
                if ($tipo === 'entrada') {
                    $service->entrada($item, $mov['areaDestino'], $mov['cantidad'], $usuario, $motivo);
                } elseif ($tipo === 'salida') {
                    $service->salida($item, $mov['areaOrigen'], $mov['cantidad'], $usuario, $motivo);
                } elseif ($tipo === 'traslado') {
                    $service->traslado($item, $mov['areaOrigen'], $mov['areaDestino'], $mov['cantidad'], $usuario, $motivo);
                } elseif ($tipo === 'ajuste') {
                    $service->ajuste($item, $mov['areaDestino'], $mov['cantidad'], $usuario, $motivo);
                }
            } catch (\Throwable $e) {
                // Ignorar movimientos que fallen por validación de stock en datos demo
                logger()->warning('Movimiento demo omitido: ' . $e->getMessage());
            }
        }
    }
}