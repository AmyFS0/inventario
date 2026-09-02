<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permisos = [
            // Empresas
            'ver empresas',
            'crear empresas',
            'editar empresas',
            'eliminar empresas',
            // Sucursales
            'ver sucursales',
            'crear sucursales',
            'editar sucursales',
            'eliminar sucursales',
            // Áreas
            'ver areas',
            'crear areas',
            'editar areas',
            'eliminar areas',
            // Categorías
            'ver categorias',
            'crear categorias',
            'editar categorias',
            'eliminar categorias',
            // Unidades de medida
            'ver unidades',
            'crear unidades',
            'editar unidades',
            'eliminar unidades',
            // Proveedores
            'ver proveedores',
            'crear proveedores',
            'editar proveedores',
            'eliminar proveedores',
            // Ítems
            'ver items',
            'crear items',
            'editar items',
            'eliminar items',
            // Movimientos
            'ver movimientos',
            'registrar entradas',
            'registrar salidas',
            'registrar traslados',
            'registrar ajustes',
            // Dashboard y reportes
            'ver dashboard',
            'ver reportes',
            'exportar reportes',
            // Usuarios
            'gestionar usuarios',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Super Administrador: todos los permisos
        $superAdmin = Role::firstOrCreate(['name' => 'super administrador']);
        $superAdmin->syncPermissions(Permission::all());

        // Administrador de Empresa: todo el CRUD de su empresa
        $administradorEmpresa = Role::firstOrCreate(['name' => 'administrador de empresa']);
        $administradorEmpresa->syncPermissions([
            'ver sucursales',
            'crear sucursales',
            'editar sucursales',
            'eliminar sucursales',
            'ver areas',
            'crear areas',
            'editar areas',
            'eliminar areas',
            'ver categorias',
            'crear categorias',
            'editar categorias',
            'eliminar categorias',
            'ver unidades',
            'crear unidades',
            'editar unidades',
            'eliminar unidades',
            'ver proveedores',
            'crear proveedores',
            'editar proveedores',
            'eliminar proveedores',
            'ver items',
            'crear items',
            'editar items',
            'eliminar items',
            'ver movimientos',
            'registrar entradas',
            'registrar salidas',
            'registrar traslados',
            'registrar ajustes',
            'ver dashboard',
            'ver reportes',
            'exportar reportes',
            'gestionar usuarios',
        ]);

        // Encargado de Área: ver y gestionar el inventario de su área
        $encargadoArea = Role::firstOrCreate(['name' => 'encargado de area']);
        $encargadoArea->syncPermissions([
            'ver areas',
            'ver items',
            'ver movimientos',
            'registrar entradas',
            'registrar salidas',
            'registrar traslados',
            'ver dashboard',
        ]);

        // Consulta / Solo Lectura
        $consulta = Role::firstOrCreate(['name' => 'consulta']);
        $consulta->syncPermissions([
            'ver dashboard',
            'ver reportes',
            'ver items',
            'ver movimientos',
            'ver areas',
            'ver sucursales',
        ]);
    }
}