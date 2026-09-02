<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $user = auth()->user();

        $usuarios = User::query()
            ->with('empresa')
            ->with('roles')
            ->withTrashed()
            ->when(!$user->esSuperAdmin(), function ($query) use ($user) {
                $query->where('empresa_id', $user->empresa_id);
            })
            ->when($request->filled('busqueda'), function ($query) use ($request) {
                $busqueda = $request->input('busqueda');
                $query->where(function ($sub) use ($busqueda) {
                    $sub->where('name', 'like', '%' . $busqueda . '%')
                        ->orWhere('email', 'like', '%' . $busqueda . '%');
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('usuarios.index', compact('usuarios'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        $user = auth()->user();

        $empresas = Empresa::query()
            ->when(!$user->esSuperAdmin(), function ($query) use ($user) {
                $query->where('id', $user->empresa_id);
            })
            ->orderBy('nombre')
            ->get();

        $roles = Role::orderBy('name')->get();

        return view('usuarios.create', compact('empresas', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $user = auth()->user();

        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'empresa_id' => ['nullable', 'exists:empresas,id'],
            'estado' => ['required', 'in:activo,inactivo'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
        ]);

        if (!$user->esSuperAdmin()) {
            $datos['empresa_id'] = $user->empresa_id;
        }

        $nuevoUsuario = User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => $datos['password'],
            'empresa_id' => $datos['empresa_id'] ?? null,
            'estado' => $datos['estado'],
        ]);

        $roles = Role::whereIn('id', $datos['roles'])->pluck('name')->all();
        $nuevoUsuario->assignRole($roles);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario): View
    {
        $this->authorize('update', $usuario);

        $user = auth()->user();

        $empresas = Empresa::query()
            ->when(!$user->esSuperAdmin(), function ($query) use ($user) {
                $query->where('id', $user->empresa_id);
            })
            ->orderBy('nombre')
            ->get();

        $roles = Role::orderBy('name')->get();

        return view('usuarios.edit', compact('usuario', 'empresas', 'roles'));
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        $this->authorize('update', $usuario);

        $user = auth()->user();

        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $usuario->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'empresa_id' => ['nullable', 'exists:empresas,id'],
            'estado' => ['required', 'in:activo,inactivo'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
        ]);

        if (!$user->esSuperAdmin()) {
            $datos['empresa_id'] = $user->empresa_id;
        }

        $usuario->name = $datos['name'];
        $usuario->email = $datos['email'];
        $usuario->empresa_id = $datos['empresa_id'] ?? null;
        $usuario->estado = $datos['estado'];

        if (!empty($datos['password'])) {
            $usuario->password = Hash::make($datos['password']);
        }

        $usuario->save();

        $roles = Role::whereIn('id', $datos['roles'])->pluck('name')->all();
        $usuario->syncRoles($roles);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario): RedirectResponse
    {
        $this->authorize('delete', $usuario);

        if ($usuario->id === auth()->id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puede eliminar su propio usuario.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}