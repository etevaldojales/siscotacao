<?php

namespace App\Http\Controllers;

use App\Models\NewJustificativa;
use Illuminate\Http\Request;

class NewJustificativaController extends Controller
{
    public function index()
    {
        return view('justificativa');
    }

    public function load()
    {
        $justificativas = NewJustificativa::all()->where('status', '=', 1);
        return response()->json($justificativas);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'descricao' => 'required|string',
                'status' => 'required|in:0,1',
            ]);

            if ($request->id == 0) {
                $justificativa = NewJustificativa::create($request->all());
            } else {
                $justificativa = NewJustificativa::find($request->id);
                $justificativa->update($request->all());
            }
            return response()->json($justificativa, 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show(Request $request)
    {
        $justificativa = NewJustificativa::findOrFail($request->id);
        return response()->json($justificativa);
    }



    public function destroy(Request $request)
    {
        try {
            $justificativa = NewJustificativa::findOrFail($request->id);
            $justificativa->status = 0;
            $justificativa->save();

            return response()->json(1);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
