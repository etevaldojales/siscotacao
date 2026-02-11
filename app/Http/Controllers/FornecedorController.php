<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Fornecedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FornecedorController extends Controller
{
    public function index()
    {
        $fornecedores = Fornecedor::all()->where('status','=', 'Ativo');
        $categorias = Categoria::all();
        return view('fornecedor.index', compact('fornecedores', 'categorias'));
    }

    public function active()
    {
        $fornecedores = Fornecedor::where('status', 'Ativo')->get();
        return response()->json($fornecedores);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $fornecedores = Fornecedor::where('status', 'Ativo')
            ->where(function ($q) use ($query) {
                $q->where('razao_social', 'LIKE', "%{$query}%")
                  ->orWhere('cnpj', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('telefone', 'LIKE', "%{$query}%")
                  ->orWhere('celular', 'LIKE', "%{$query}%");
            })
            ->get();

        return response()->json($fornecedores);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cnpj' => 'required|string|max:20|unique:fornecedores,cnpj,' . $request->id,
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'email2' => 'nullable|email|max:255',
            'inscricao_estadual' => 'nullable|string|max:50',
            'cep' => 'nullable|string|max:20',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'tipo' => 'required|in:filial,matriz',
            'cnpj_matriz' => 'nullable|string|max:20',
        ]);

        if ($request->id == 0) {
            $fornecedor = new Fornecedor($request->all());
            $fornecedor->status = 'Ativo';
            $fornecedor->save();
        } elseif ($request->id > 0) {
            $fornecedor = Fornecedor::find($request->id);

            $fornecedor->fill($request->all());
            $fornecedor->status = 'Ativo';
            $fornecedor->save();
        }

        // Sync categories
        $categorias = $request->input('categorias', []);
        $fornecedor->categorias()->sync($categorias);

        return response()->json($fornecedor);
    }

    public function get(Request $request)
    {
        $fornecedor = Fornecedor::with('categorias')->findOrFail($request->id);
        return response()->json($fornecedor);
    }

    public function destroy(Request $request)
    {
        $fornecedor = Fornecedor::findOrFail($request->id);
        $fornecedor->status = 'Inativo';
        $fornecedor->save();

        return response()->json(['success' => true]);
    }
}
