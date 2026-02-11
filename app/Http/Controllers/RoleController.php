<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {


        if ($request->id == 0) {
            $validatedData = $request->validate([
                'name' => 'required|unique:roles,name',
                'description' => 'nullable|string',
            ]);
            $role = Role::create($request->only('name', 'description'));
            //return redirect()->route('roles.index')->with('success', 'Role created successfully.');
            return response()->json(['message' => 'Perfil cadastrado com sucesso!', 'role' => $role], 201);

        } elseif ($request->id > 0) {
            $validatedData = $request->validate([
                'name' => 'required|string',
                'description' => 'nullable|string',
            ]);            
            $role = Role::find($request->id);
            if (!$role) {
                return response()->json(['message' => 'Perfil não encontrado.'], 404);
            }

            $role->update($validatedData);

            //$role->update($request->only('name', 'description'));
            //return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
            return response()->json(['message' => 'Perfil atualizado com sucesso!', 'role' => $role]);
        }
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
        ]);

        $role->update($request->only('name', 'description'));

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
