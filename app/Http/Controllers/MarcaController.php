<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function index()
    {
        $marcas = Marca::all()->where('ativo', 1);
        return view('marca.index', compact('marcas'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $marcas = Marca::where('ativo', 1)
            ->where('nome', 'LIKE', "%{$query}%")
            ->get();

        return response()->json($marcas);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'ativo' => 'required|boolean',
        ]);

        if ($request->id == 0) {
            $marca = new Marca($request->all());
            $marca->save();
        } elseif ($request->id > 0) {
            $marca = Marca::find($request->id);
            $marca->fill($request->all());
            $marca->save();
        }
        return response()->json($marca);
    }

    public function get(Request $request)
    {
        $marca = Marca::findOrFail($request->id);
        return response()->json($marca);
    }

    public function destroy(Request $request)
    {
        $marca = Marca::findOrFail($request->id);
        $marca->ativo = 0;
        $marca->save();

        return response()->json(['success' => true]);
    }
}
