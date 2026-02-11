<?php



namespace App\Http\Controllers;



use App\Models\Categoria;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;



class CategoriaController extends Controller

{

    public function index()

    {

        $categorias = Categoria::all();

        return view('categoria.index', compact('categorias'));

    }



    public function store(Request $request)

    {



        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'cnpj_comprador' => 'nullable|string|max:20',
        ]);



        if ($request->id == 0) {

            $categoria = new Categoria();

            $categoria->user_id = Auth::id();

            $categoria->nome = $request->nome;

            $categoria->descricao = $request->descricao;

            $categoria->cnpj_comprador = $request->cnpj_comprador;

            $categoria->save();

        } elseif ($request->id > 0) {

            $categoria = Categoria::find($request->id);

            $categoria->nome = $request->nome;

            $categoria->descricao = $request->descricao;

            $categoria->cnpj_comprador = $request->cnpj_comprador;

            $categoria->user_id = Auth::id();

            $categoria->save();



            if ($request->has('users_comprador')) {

                $categoria->usersComprador()->sync($request->users_comprador);

            }

        }

        return response()->json($categoria);

    }



    public function show($id)

    {

        $categoria = Categoria::with('usersComprador')->findOrFail($id);

        return response()->json($categoria);

    }



    public function update(Request $request, $id)

    {

        $categoria = Categoria::findOrFail($id);



        $request->validate([

            'nome' => 'required|string|max:255',

            'descricao' => 'nullable|string',

        ]);



        $categoria->update($request->all());



        return response()->json($categoria);

    }



    public function destroy(Request $request)

    {

        $categoria = Categoria::findOrFail($request->id);

        $categoria->st_categoria = 0;

        $categoria->save();



        return response()->json(['success' => true]);

    }

}

