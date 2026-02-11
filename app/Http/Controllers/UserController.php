<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users with their roles.
     */
    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();
        return view('users.index', compact('users', 'roles'));
    }


    public function getUser(Request $request)
    {
        $user = User::find($request->id);
        return response()->json($user);
    }

    /**
     * Update the roles assigned to a user.
     */
    public function updateRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('users.index')->with('success', 'User roles updated successfully.');
    }

    /**
     * Update user data.
     */
    public function saveUser(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'descricao' => 'nullable|string|max:255',
                'empresa' => 'nullable|string|max:255',
                'cnpj' => 'required|string|max:20',
                'password' => 'nullable|string|min:8|confirmed',
            ]);

            if (!$this->checkCnpj($request->cnpj)) {
                return response()->json(['message' => 'CNPJ inválido'], 422);
            }

            $user = User::find($request->user_id);
            if (!$user) {
                return response()->json(['message' => 'Usuário não encontrado'], 404);
            } else {
                $user->name = $request->name;
                $user->descricao = $request->descricao;
                $user->empresa = $request->empresa;
                $user->cnpj = $request->cnpj;

                // Update password if provided
                if (!empty($request->password)) {
                    $user->password = Hash::make($request->password);
                }

                $user->save();

                return response()->json(['message' => 'Usuário alterado com sucesso']);

            }
            //return response()->json($request);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao salvar usuário: ' . $e->getMessage()]);
        }
    }

    /**
     * Validate CNPJ format and digits.
     */
    private function checkCnpj($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        if (strlen($cnpj) != 14) {
            return false;
        }

        // Eliminate known invalid CNPJs
        if (preg_match('/^(\\d)\\1{13}$/', $cnpj)) {
            return false;
        }

        // Validate first check digit
        $sum = 0;
        $weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;
        if ($cnpj[12] != $digit1) {
            return false;
        }

        // Validate second check digit
        $sum = 0;
        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;
        if ($cnpj[13] != $digit2) {
            return false;
        }

        return true;
    }


}
