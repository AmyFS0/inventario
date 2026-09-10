# Bitácora de Aprendizaje — Sistema de Control de Inventario para Mipymes

## Información General del Proyecto

| Campo | Descripción |
|---|---|
| **Nombre** | Sistema de Control de Inventario para Mipymes |
| **Stack** | Laravel 12, PHP 8.2, MySQL 9.7, Blade, Tailwind CSS, Vite |
| **Paquetes principales** | Laravel Breeze, Spatie Laravel-Permission, DomPDF, Maatwebsite/Excel |
| **Propósito** | Gestionar inventario multi-empresa con control por sucursales y áreas |

---

## Fase 1 — Scaffolding y Configuración Inicial

### 1.1 Creación del proyecto con Laravel 12

**Comando utilizado:**
```bash
composer create-project laravel/laravel sistemaInventario
```

**Conceptos aprendidos:**
- **Composer** es el gestor de dependencias de PHP. Permite instalar librerías de terceros y gestionar versiones.
- **Laravel 12** es un framework MVC (Modelo-Vista-Controlador) que ofrece una estructura organizada para aplicaciones web.
- **artisan** es la línea de comandos de Laravel para ejecutar tareas comunes como crear archivos, ejecutar migraciones y más.
- El comando `composer create-project` descarga Laravel y todas sus dependencias en una carpeta llamada `sistemaInventario`.

### 1.2 Configuración del entorno (.env)

**Archivo:** `.env`

**Conceptos aprendidos:**
- El archivo `.env` almacena variables de configuración del entorno (contraseñas, claves, puertos, etc.).
- Nunca se debe subir el `.env` a repositorios públicos porque contiene información sensible.
- Laravel usa `php artisan key:generate` para crear una clave de cifrado única que protege las sesiones y los tokens.
- Se configuró la conexión a MySQL con los parámetros: `DB_CONNECTION=mysql`, `DB_DATABASE=sistema_inventario`, `DB_USERNAME=root`, `DB_PASSWORD=1234`.

### 1.3 Instalación de dependencias

**Comandos:**
```bash
composer install    # Dependencias PHP
npm install         # Dependencias JavaScript
```

**Conceptos aprendidos:**
- `composer install` lee el archivo `composer.json` e instala todas las librerías PHP necesarias en la carpeta `vendor/`.
- `npm install` lee `package.json` e instala las dependencias JavaScript (como Tailwind CSS y Vite) en la carpeta `node_modules/`.
- `package-lock.json` y `composer.lock` garantizan que todos los desarrolladores usen exactamente las mismas versiones de dependencias.
- Las carpetas `vendor/` y `node_modules/` nunca se suben al repositorio porque se pueden regenerar con los comandos anteriores.

### 1.4 Compilación de assets con Vite

**Comando:**
```bash
npm run build
```

**Conceptos aprendidos:**
- **Vite** es un empaquetador de assets moderno que compila CSS, JavaScript y otros recursos.
- **Tailwind CSS** es un framework CSS que permite crear interfaces rápidamente con clases utilitarias.
- Vite toma los archivos fuente de `resources/css/` y `resources/js/`, los procesa y genera archivos optimizados en `public/build/`.
- El archivo `vite.config.js` define cómo se compilan los assets (plugins, configuración de CSS, etc.).
- Durante desarrollo se usa `npm run dev` (servidor con hot reload); para producción se usa `npm run build` (archivos mínimos y optimizados).

---

## Fase 2 — Autenticación y Seguridad

### 2.1 Laravel Breeze

**Comando:**
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
```

**Conceptos aprendidos:**
- **Laravel Breeze** es un paquete oficial que genera el sistema de autenticación completo: login, registro, recuperación de contraseña, verificación de correo y perfil.
- Se instaló con el stack **Blade** (el motor de plantillas de Laravel), que genera vistas HTML con sintaxis de PHP embebida.
- Breeze crea automáticamente las rutas en `routes/auth.php` y los controladores en `app/Http/Controllers/Auth/`.
- Las vistas de autenticación se generan en `resources/views/auth/` (login, register, forgot-password, etc.).

### 2.2 Motor de plantillas Blade

**Archivo de ejemplo:** `resources/views/layouts/app.blade.php`

**Conceptos aprendidos:**
- **Blade** es el motor de plantillas de Laravel. Permite mezclar HTML con directivas PHP de forma limpia.
- `{{ $variable }}` imprime una variable escapada (previene ataques XSS).
- `@yield('nombre')` define un espacio que será llenado por secciones en las vistas hijas.
- `{{ $slot }}` se usa en componentes anónimos (como el layout principal) para insertar contenido dinámico.
- `@csrf` genera un token de seguridad para formularios POST.
- `@method('PUT')` simula un método HTTP PUT en formularios HTML (que solo soportan GET y POST).
- `@if`, `@foreach`, `@auth`, `@guest` son directivas de control de flujo de Blade.

### 2.3 Gestión de sesiones

**Conceptos aprendidos:**
- Laravel maneja sesiones automáticamente. Cada usuario recibe una cookie con un ID de sesión.
- El token CSRF (Cross-Site Request Form) se genera con `@csrf` y se valida automáticamente en cada petición POST.
- Si el token no coincide con el de la sesión, Laravel retorna un error **419** (Page Expired).
- Las sesiones se almacenan en la base de datos o en archivos temporales (configurado en `config/session.php`).

### 2.4 Protección CSRF

**Concepto:**
- **CSRF** (Cross-Site Request Forgery) es un ataque donde un sitio malicioso envía peticiones falsas usando la sesión de otro usuario.
- Laravel protege contra esto generando un token único por sesión que debe enviarse en cada formulario.
- El middleware `VerifyCsrfToken` verifica que el token enviado coincida con el de la sesión.

### 2.5 Hash de contraseñas

**Concepto:**
- Laravel nunca almacena contraseñas en texto plano. Usa **bcrypt** o **argon2** para hashearlas.
- `bcrypt('password')` genera un hash irreversible que se compara con el valor de la base de datos.
- El método `Hash::make()` crea el hash, y `Hash::check()` compara una contraseña con su hash.

---

## Fase 3 — Control de Roles y Permisos

### 3.1 Spatie Laravel-Permission

**Comando:**
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

**Conceptos aprendidos:**
- **Spatie Laravel-Permission** es un paquete que permite definir roles (Super Admin, Administrador, Encargado de Área, Consulta) y permisos granulares.
- Crea las tablas: `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`.
- Cada usuario puede tener múltiples roles, y cada rol puede tener múltiples permisos.
- Se configuró el trait `HasRoles` en el modelo `User` para acceder a métodos como `$user->hasRole('admin')` o `$user->can('create-items')`.

### 3.2 Seeder de roles y permisos

**Archivo:** `database/seeders/RolesAndPermissionsSeeder.php`

**Conceptos aprendidos:**
- Los **Seeders** son clases que poblan la base de datos con datos iniciales.
- Se crearon 4 roles: `super_admin`, `admin_empresa`, `encargado_area`, `consulta`.
- Se crearon 37 permisos específicos como `create-empresas`, `edit-items`, `view-movimientos`, etc.
- El seeder asigna permisos a cada rol según su nivel de acceso.
- Se ejecuta con `php artisan db:seed`.

### 3.3 Autorización con Policies

**Conceptos aprendidos:**
- Las **Policies** (políticas) definen reglas de autorización para cada modelo. Por ejemplo: solo el propietario de una empresa puede editarla.
- Cada Policy tiene métodos como `viewAny()`, `view()`, `create()`, `update()`, `delete()` que retornan `true` o `false`.
- Se usan en los controladores con `$this->authorize('update', $empresa)`.
- El middleware `auth` protege rutas que requieren login.
- La función `auth()->id()` retorna el ID del usuario autenticado.

---

## Fase 4 — Modelado de Base de Datos

### 4.1 Migraciones

**Conceptos aprendidos:**
- Las **migraciones** son archivos PHP que definen la estructura de las tablas de la base de datos.
- Se crean con `php artisan make:migration create_empresas_table`.
- Cada migración tiene dos métodos: `up()` (crear tabla) y `down()` (eliminar tabla).
- Se ejecutan con `php artisan migrate` y se revierten con `php artisan migrate:rollback`.
- `php artisan migrate:fresh --seed` elimina todo, recrea las tablas y ejecuta los seeders (ideal para desarrollo).

### 4.2 Tipos de columnas y restricciones

**Conceptos aprendidos:**
- `$table->id()` crea una columna autoincremental BIGINT.
- `$table->foreignId('empresa_id')->constrained()` crea una llave foránea que apunta a `empresas.id`.
- `$table->string('nombre')` crea una columna VARCHAR(255).
- `$table->decimal('costo_unitario', 10, 2)` crea un decimal con 10 dígitos totales y 2 decimales.
- `$table->enum('estado', ['activo', 'inactivo'])` crea una columna con valores predefinidos.
- `$table->timestamps()` agrega `created_at` y `updated_at` automáticamente.
- `$table->softDeletes()` agrega `deleted_at` para eliminación lógica.
- `$table->unique(['empresa_id', 'sku'])` crea un índice único compuesto.

### 4.3 Modelos Eloquent

**Conceptos aprendidos:**
- **Eloquent** es el ORM (Object-Relational Mapping) de Laravel. Mapea tablas de BD a clases PHP.
- Cada modelo representa una tabla: `User`, `Empresa`, `Sucursal`, `Area`, `Item`, etc.
- `protected $fillable` define qué campos se pueden asignar masivamente (previene asignación masiva maliciosa).
- `protected $casts` convierte tipos de BD a tipos PHP (ej: `'costo_unitario' => 'decimal:2'`).
- `protected $table` permite especificar el nombre de la tabla cuando no sigue la convención.
- `use SoftDeletes` agrega eliminación lógica al modelo.

### 4.4 Relaciones entre modelos

**Conceptos aprendidos:**
- `hasOne` / `belongsTo`: relación uno a uno (ej: `Area` tiene un `User` encargado).
- `hasMany` / `belongsTo`: relación uno a muchos (ej: `Empresa` tiene muchas `Sucursales`).
- `belongsToMany`: relación muchos a muchos a través de una tabla pivote (ej: `Item` ↔ `Area` a través de `inventario_area`).
- `hasManyThrough`: relación indirecta (ej: `Empresa` → `Sucursal` → `Area`, accediendo a áreas desde la empresa).
- En la relación `belongsToMany`, el modelo define `$table`, `$foreignKey` y `$relatedKey` en la tabla pivote.

### 4.5 Eliminación lógica (Soft Delete)

**Concepto:**
- En lugar de eliminar registros físicamente, se marca la columna `deleted_at` con la fecha de eliminación.
- Los registros con `deleted_at` lleno se comportan como "eliminados" pero permanecen en la BD.
- Permite recuperar registros eliminados accidentalmente.
- Se implementa con `use SoftDeletes` en el modelo y `$table->softDeletes()` en la migración.
- Las consultas de Eloquent excluyen automáticamente los registros eliminados.

---

## Fase 5 — Controladores y Lógica de Negocio

### 5.1 Controladores RESTful

**Comando:**
```bash
php artisan make:controller EmpresaController --model=Empresa
```

**Conceptos aprendidos:**
- Un **Controlador** es una clase que agrupa la lógica relacionada con un recurso.
- Los controladores RESTful siguen una convención de métodos:
  - `index()`: listar todos los registros (GET /recursos)
  - `create()`: mostrar formulario de creación (GET /recursos/create)
  - `store()`: guardar un nuevo registro (POST /recursos)
  - `show()`: mostrar un registro (GET /recursos/{id})
  - `edit()`: mostrar formulario de edición (GET /recursos/{id}/edit)
  - `update()`: actualizar un registro (PUT /recursos/{id})
  - `destroy()`: eliminar un registro (DELETE /recursos/{id})
- Se crearon 11 controladores: Dashboard, Empresa, Sucursal, Area, Categoria, UnidadMedida, Proveedor, Item, MovimientoInventario, Reporte, User.

### 5.2 Inyección de dependencias

**Conceptos aprendidos:**
- Laravel resuelve automáticamente las dependencias de los controladores mediante **inyección de dependencias**.
- Cuando escribes `public function show(Empresa $empresa)`, Laravel busca el registro automáticamente y lo inyecta.
- Si no encuentra el registro, retorna un error 404 automáticamente.
- Esto reemplaza el código manual de buscar registros con `Empresa::findOrFail($id)`.

### 5.3 Servicio de Inventario (InventarioService)

**Archivo:** `app/Services/InventarioService.php`

**Conceptos aprendidos:**
- Un **Service** (servicio) es una clase que encapsula la lógica de negocio compleja.
- Se separa la lógica de los controladores para mantenerlos delgados (thin controllers).
- El `InventarioService` maneja las 4 operaciones de inventario:
  - **Entrada**: incrementa stock en un área, o crea el registro en `inventario_area` si no existe.
  - **Salida**: decrementa stock (valida que haya suficiente).
  - **Traslado**: descuenta del área origen e incrementa en el área destino.
  - **Ajuste**: modifica el stock con un motivo obligatorio.
- Se usa transacción de base de datos (`DB::transaction()`) para asegurar que todas las operaciones se completen o ninguna se aplique.

### 5.4 Validación de datos

**Conceptos aprendidos:**
- Laravel valida datos en el controlador con `$request->validate([...])`.
- Si la validación falla, redirige automáticamente al formulario con errores en la sesión.
- En Blade se muestran con `@error('campo') {{ $message }} @enderror`.
- Reglas comunes: `required` (obligatorio), `string` (texto), `email` (correo válido), `max:255`, `unique:table,column` (valor único en BD), `exists:table,column` (debe existir en BD).

### 5.5 Manejo de errores

**Conceptos aprendidos:**
- Laravel tiene un sistema de excepciones que captura errores automáticamente.
- `abort(404)` muestra la página de "No encontrado".
- `abort(403)` muestra "Acceso prohibido".
- Los errores se registran en `storage/logs/laravel.log`.
- Las sesiones flash (`session()->flash('success', 'Mensaje')`) muestran mensajes temporales después de una acción.

---

## Fase 6 — Rutas y Navegación

### 6.1 Rutas RESTful

**Archivo:** `routes/web.php`

**Conceptos aprendidos:**
- Las rutas definen qué URL ejecuta qué controlador.
- `Route::resource('empresas', EmpresaController::class)` crea automáticamente las 7 rutas RESTful.
- Las rutas pueden agruparse con middleware: `Route::middleware(['auth'])->group(function() { ... })`.
- Las rutas nombradas (`->name('empresas.index')`) permiten generar URLs con `route('empresas.index')`.
- Se crearon rutas personalizadas para movimientos (`Route::post('/movimientos/entrada', ...)`) y reportes.

### 6.2 Middleware

**Conceptos aprendidos:**
- Los **Middleware** son capas intermedias que procesan cada petición HTTP antes de llegar al controlador.
- `auth`: verifica que el usuario esté autenticado.
- `verified`: verifica que el usuario haya verificado su correo.
- `verified`: middleware de Breeze que exige verificación de email.
- `spatie.permission`: middleware de Spatie para verificar roles y permisos.
- Los middleware se aplican a rutas individuales o grupos de rutas.

### 6.3 Generación de URLs

**Conceptos aprendidos:**
- `route('empresas.index')` genera la URL `/empresas` a partir del nombre de la ruta.
- `route('empresas.show', $empresa)` genera `/empresas/1` pasando el modelo.
- `asset('images/logo.png')` genera la URL completa de un recurso estático.
- `url('/dashboard')` genera una URL absoluta.

---

## Fase 7 — Vistas Blade y Diseño

### 7.1 Estructura de layouts

**Conceptos aprendidos:**
- Un **layout** es una plantilla base que comparten todas las páginas (sidebar, topbar, footer).
- `resources/views/layouts/app.blade.php` es el layout principal con `{{ $slot }}`.
- `resources/views/layouts/sidebar.blade.php` es el menú lateral con navegación.
- `resources/views/layouts/topbar.blade.php` es la barra superior con perfil de usuario.
- Las vistas hijas extienden el layout con `@extends('layouts.app')` o usan el componente `x-app-layout`.

### 7.2 Componentes Blade

**Conceptos aprendidos:**
- Los **componentes Blade** son piezas reutilizables de HTML.
- `x-text-input`, `x-input-label`, `x-input-error` son componentes de formularios.
- `x-primary-button`, `x-danger-button`, `x-secondary-button` son botones estilizados.
- Se crean en `resources/views/components/` o se usan los de Breeze/Tailwind.
- `@stack('styles')` y `@stack('scripts')` permiten inyectar CSS/JS adicionales desde vistas hijas.

### 7.3 Formularios HTML en Blade

**Conceptos aprendidos:**
- Los formularios usan `<form method="POST" action="{{ route('empresas.store') }}">`.
- `@csrf` genera el token de seguridad.
- `@method('PUT')` simula un PUT (los formularios HTML solo soportan GET y POST).
- Los campos usan componentes Blade como `<x-text-input type="text" name="nombre" :value="old('nombre')" />`.
- `old('campo')` recupera el valor anterior del campo después de una validación fallida.

### 7.4 Diseño responsivo con Tailwind CSS

**Conceptos aprendidos:**
- **Tailwind CSS** usa clases utilitarias como `flex`, `grid`, `p-4`, `bg-white`, `rounded-lg`.
- `md:grid-cols-2` aplica estilos solo en pantallas medianas o mayores.
- `hidden md:block` oculta en móvil y muestra en escritorio.
- `text-sm lg:text-base` cambia el tamaño de texto según el dispositivo.
- El layout usa un sidebar colapsable en móvil con botón hamburguesa.

---

## Fase 8 — Lógica de Movimientos de Inventario

### 8.1 Los 4 tipos de movimiento

| Tipo | Descripción | Efecto en stock |
|---|---|---|
| **Entrada** | Ingreso de stock a un área | +cantidad en área destino |
| **Salida** | Retiro de stock de un área | -cantidad en área origen |
| **Traslado** | Mueve stock entre áreas de la misma empresa | -cantidad origen, +cantidad destino |
| **Ajuste** | Corrección manual de stock | ±cantidad según ajuste |

**Conceptos aprendidos:**
- Cada movimiento queda registrado en `movimientos_inventario` como bitácora permanente.
- La tabla de movimientos **nunca se edita ni se borra** (es el historial oficial de auditoría).
- `MovimientoInventario` tiene `$timestamps = false` porque solo usa `created_at`.
- El campo `tipo` es un enum: `entrada`, `salida`, `traslado`, `ajuste`.

### 8.2 Tabla pivote inventario_area

**Conceptos aprendidos:**
- La tabla `inventario_area` es una tabla pivote que almacena la cantidad de un ítem en un área específica.
- Tiene `item_id`, `area_id` y `cantidad`.
- Si no existe un registro para un ítem/área, se crea al hacer la primera entrada.
- El stock total de un ítem se calcula sumando `cantidad` de todas las áreas.

### 8.3 AJAX para consulta de stock en tiempo real

**Conceptos aprendidos:**
- Los formularios de movimiento usan JavaScript con la API `fetch` para consultar el stock disponible.
- Cuando el usuario selecciona un ítem y un área, se hace una petición GET a `/movimientos/stock-disponible?item_id=1&area_id=1`.
- El servidor retorna `{"disponible": 95, "unidad": "l"}` en formato JSON.
- El JavaScript actualiza la interfaz mostrando el stock disponible antes de confirmar la operación.
- Esto previene errores como intentar sacar más stock del disponible.

### 8.4 Validaciones de negocio

**Conceptos aprendidos:**
- **Stock insuficiente**: no se puede hacer salida/traslado/ajuste negativo si no hay stock suficiente.
- **Ítem con stock**: no se puede eliminar un ítem, área o sucursal que tenga stock diferente de cero.
- **Motivo obligatorio**: los ajustes requieren un motivo porque modifican el stock sin ser entrada ni salida.
- **Trazabilidad**: cada movimiento queda vinculado al usuario que lo ejecutó.

---

## Fase 9 — Dashboard y Visualización de Datos

### 9.1 Tarjetas de resumen

**Conceptos aprendidos:**
- El dashboard muestra tarjetas con KPIs: total de ítems, total de unidades, alertas de stock.
- Se consultan datos desde el controlador (`DashboardController`) y se pasan a la vista.
- Los colores indican estado: verde (normal), amarillo (cerca del mínimo), rojo (crítico).

### 9.2 Gráficos con Chart.js

**Conceptos aprendidos:**
- **Chart.js** es una librería JavaScript para crear gráficos interactivos.
- Se incluye con `<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>`.
- Se creó un gráfico doughnut que muestra la distribución de inventario por categoría.
- Los datos se pasan desde PHP a JavaScript usando `json_encode()` dentro de un bloque `@push('scripts')`.

### 9.3 Alertas de stock mínimo

**Conceptos aprendidos:**
- Se compara `inventario_area.cantidad` con `items.stock_minimo`.
- Si la cantidad está por debajo del mínimo, se muestra una alerta visual.
- El sistema clasifica en tres niveles: stock alto (verde), stock bajo (amarillo), stock crítico (rojo).

---

## Fase 10 — Reportes y Exportaciones

### 10.1 Reportes con filtros

**Conceptos aprendidos:**
- Los reportes permiten filtrar por empresa, sucursal, área, categoría, tipo de movimiento, rango de fechas.
- Se usan consultas Eloquent encadenadas: `Item::when($empresa, fn($q) => $q->where('empresa_id', $empresa))`.
- Los filtros se envían por GET y se leen con `$request->input('filtro')`.

### 10.2 Exportación a PDF con DomPDF

**Comando:**
```bash
composer require barryvdh/laravel-dompdf
```

**Conceptos aprendidos:**
- **DomPDF** convierte HTML en PDF del lado del servidor.
- Se crea una vista Blade independiente en `resources/views/reportes/pdf/` con HTML standalone.
- Se renderiza con `PDF::loadView('reportes.pdf.inventario', $datos)->download('inventario.pdf')`.
- Las vistas PDF usan HTML/CSS estándar (no Tailwind) porque DomPDF tiene soporte limitado de CSS3.
- Se definen márgenes y orientación de página con `->setPaper('letter', 'portrait')`.

### 10.3 Exportación a Excel con Maatwebsite

**Comando:**
```bash
composer require maatwebsite/excel
```

**Conceptos aprendidos:**
- **Maatwebsite Excel** permite exportar datos a archivos `.xlsx` o `.csv`.
- Se crea una clase Export que implementa `FromCollection` y `WithHeadings`.
- `FromCollection` define los datos (colección Eloquent).
- `WithHeadings` define los encabezados de las columnas.
- Se exporta con `Excel::download(new InventarioExport($datos), 'inventario.xlsx')`.
- Se usa `ShouldAutoSize` para ajustar automáticamente el ancho de columnas.

---

## Fase 11 — Control de Versiones con Git

### 11.1 Inicialización del repositorio

**Comandos:**
```bash
git init
git remote add origin https://github.com/AmyFS0/inventario.git
```

**Conceptos aprendidos:**
- **Git** es un sistema de control de versiones que registra cambios en archivos a lo largo del tiempo.
- `git init` inicializa un repositorio Git en la carpeta actual.
- Un **commit** es un "punto de guardado" del proyecto con un mensaje descriptivo.
- Un **remote** es el repositorio remoto (GitHub) donde se almacena el código.
- `git push` sube los commits locales al repositorio remoto.

### 11.2 Organización de commits por módulo

**Estructura de commits creada:**

| # | Commit | Contenido |
|---|--------|-----------|
| 1 | Scaffolding | Laravel 12 + Breeze + dependencias |
| 2 | Base de datos | Migraciones + Seeders |
| 3 | Modelos Eloquent | 10 modelos con relaciones |
| 4 | Autorización | Policies + InventarioService |
| 5 | Controladores | 22 controladores |
| 6 | Rutas | web.php + auth.php |
| 7 | Layouts y Dashboard | Sidebar, topbar, componentes |
| 8 | CRUD Empresas/Sucursales/Áreas | 12 vistas |
| 9 | CRUD Categorías/Unidades/Proveedores | 9 vistas |
| 10 | Vistas Ítems | Index, create, edit, show |
| 11 | Vistas Movimientos | Formularios con AJAX |
| 12 | Reportes y Exportaciones | PDF + Excel |
| 13 | Usuarios y Auth | CRUD + vistas Breeze |
| 14 | Archivos restantes | Requests, Providers, Components |

**Conceptos aprendidos:**
- Cada commit representa un avance lógico e independiente del proyecto.
- Los mensajes de commit empiezan con un número y descripción clara del módulo.
- `git add` añade archivos al "staging area" (área de preparación).
- `git commit -m "mensaje"` crea un commit con los archivos preparados.
- `git push` sube los commits al repositorio remoto en GitHub.

### 11.3 Archivo .gitignore

**Concepto:**
- El archivo `.gitignore` lista archivos/carpetas que Git debe ignorar.
- Se excluyen: `.env` (datos sensibles), `vendor/` (dependencias PHP), `node_modules/` (dependencias JS), `public/build/` (assets compilados).
- Esto evita subir archivos generados o sensibles al repositorio.

---

## Fase 12 — Conceptos de Multi-Tenancy

### 12.1 Arquitectura multi-tenant por empresa

**Conceptos aprendidos:**
- **Multi-tenancy** significa que múltiples "inquilinos" (empresas) comparten la misma aplicación.
- Cada registro de negocio tiene `empresa_id` para pertenecer a una empresa específica.
- El Super Admin ve todo; los demás usuarios solo ven los datos de su empresa.
- Las Policies verifican que el usuario autenticado pertenece a la misma empresa que el registro que intenta modificar.
- Esta arquitectura permite agregar nuevas empresas sin cambios estructurales.

### 12.2 Filtrado por empresa

**Concepto:**
- En cada consulta, se filtra por `empresa_id = auth()->user()->empresa_id`.
- Esto asegura que un usuario de empresa A nunca vea los datos de empresa B.
- El Super Admin (sin empresa asignada) ve todos los registros.

---

## Fase 13 — Buenas Prácticas Aprendidas

### 13.1 Principios SOLID aplicados

- **Responsabilidad Única**: cada controlador maneja solo un recurso.
- **Abierto/Cerrado**: los services extienden la lógica sin modificar los controladores.
- **Inversión de Dependencias**: se inyectan modelos en lugar de instanciarlos manualmente.

### 13.2 Convenciones de Laravel

- Modelos en singular (`Empresa`), tablas en plural (`empresas`).
- Controllers en singular + `Controller` (`EmpresaController`).
- Vistas en carpeta plural (`empresas/index.blade.php`).
- Rutas nombradas con punto (`empresas.index`, `empresas.store`).

### 13.3 Seguridad

- Tokens CSRF en todos los formularios.
- Validación de datos en el servidor (nunca confiar en el cliente).
- Hash de contraseñas con bcrypt.
- Políticas de autorización en cada recurso.
- Soft deletes para preservar datos.

### 13.4 Rendimiento

- Paginación en todos los listados (`->paginate(15)`).
- Eager loading con `with()` para evitar el problema N+1.
- Índices en columnas de búsqueda y llaves foráneas.

---

## Resumen de Tecnologías Aprendidas

| Categoría | Tecnologías |
|---|---|
| **Backend** | PHP 8.2, Laravel 12, Eloquent ORM, Artisan CLI |
| **Frontend** | Blade, HTML5, CSS3, Tailwind CSS, JavaScript |
| **Base de datos** | MySQL 9.7, Migraciones, Seeders |
| **Autenticación** | Laravel Breeze, Sesiones, CSRF, Hashing |
| **Autorización** | Spatie Laravel-Permission, Policies, Roles/Permisos |
| **Reportes** | DomPDF (PDF), Maatwebsite/Excel (Excel) |
| **Gráficos** | Chart.js |
| **Frontend Tooling** | Vite, npm, Node.js |
| **Control de versiones** | Git, GitHub |
| **Patrones de diseño** | MVC, Repository Pattern (Services), Soft Deletes, Multi-Tenancy |
