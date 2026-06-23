<?php

namespace App\Http\Controllers;

use App\Models\MenuPermission;
use App\Models\Role;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index()
    {
        return view('roles.index', [
            'roles' => Role::query()->with('menuPermissions')->orderBy('name')->get(),
            'permissions' => MenuPermission::query()->orderBy('label')->get(),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:menu_permissions,key'],
        ]);

        $role->syncMenuPermissions($validated['permissions'] ?? []);

        return back()->with('status', 'Permissões atualizadas com sucesso.');
    }
}
