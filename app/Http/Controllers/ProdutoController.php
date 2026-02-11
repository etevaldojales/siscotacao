<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Marca;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = $request->user();

        $query = Produto::query()
            ->with(['user', 'categoria', 'marcas'])
            ->where('status', 'Ativo');

        if ($user && $user->isAdmin()) {
            // Admin: load all products with status 'Ativo'
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            }
            $query->where('status', '=', 'Ativo');
        } else {
            // Other users: no products
            $query->whereRaw('1 = 0'); // no results
        }

        $produtos = $query->orderBy('name')->paginate(10)->withQueryString();

        if ($user && $user->isAdmin()) {
            $categorias = Categoria::orderBy('nome')->get();
        } 
        else {
            $categorias = collect();
        }
        $marcas = Marca::where('ativo', 1)->orderBy('nome')->get();

        if ($request->ajax()) {
            $view = view('partials.product_table', compact('produtos'))->render();
            return response()->json(['html' => $view]);
        }

        return view('produto', compact('produtos', 'search', 'categorias', 'marcas'));
    }

    /**
     * Return products and categories filtered by logged-in comprador user as JSON.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductsAndCategoriesForComprador(Request $request)
    {
         //\Log::info("aqui");
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($user->isAdmin()) {
            $produtos = Produto::where('status', 'Ativo')->orderBy('name')->get();
            $categorias = Categoria::orderBy('nome')->get();
        } else if ($user->hasRole('comprador')) {
           
            $produtos = Produto::where('status', 'Ativo')
                ->whereHas('compradores', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->orderBy('name')
                ->get();

            $categorias = Categoria::whereHas('usersComprador', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->orderBy('nome')->get();
            \Log::info('Produtos: ' . $produtos);
        } else {
            $produtos = collect();
            $categorias = collect();
        }

        return response()->json([
            'produtos' => $produtos,
            'categorias' => $categorias,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $retorno = [];
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            return response()->json(['message' => 'Ação não autorizada.'], 403);
        }

        $validatedData = $request->validate([
            'codigo' => 'required|integer|min:0|unique:products,codigo',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|string|in:Ativo,Inativo',
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categorias,id',
            'marcas' => 'nullable|array',
            'marcas.*' => 'exists:marcas,id',
            'cnpj_comprador' => 'nullable|string',
        ]);

        if ($request->id == 0) {
            $produto = Produto::create($validatedData);
            if (isset($validatedData['marcas'])) {
                $produto->marcas()->sync($validatedData['marcas']);
            }
            else {
                $id_marca = Marca::where('nome', 'Sem Marca')->first()->id;
                $produto->marcas()->sync([$id_marca]);
            }
            if (isset($validatedData['cnpj_comprador'])) {
                $produto->cnpj_comprador = $validatedData['cnpj_comprador'];
                $produto->save();
            }
            $retorno = ['message' => 'Produto cadastrado com sucesso!', 'produto' => $produto];
            
        } else if ($request->id > 0) {
            $produto = Produto::find($request->id);
            if (!$produto) {
                $retorno = ['message' => 'Produto não encontrado.'];
            }

            $validatedData = $request->validate([
                'codigo' => 'required|integer|min:0|unique:products,codigo',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'status' => 'required|string|in:Ativo,Inativo',
                'user_id' => 'required|exists:users,id',
                'category_id' => 'required|exists:categorias,id',
                'marcas' => 'nullable|array',
                'marcas.*' => 'exists:marcas,id',
                'cnpj_comprador' => 'nullable|string',
            ]);

            $produto->update($validatedData);
            if (isset($validatedData['marcas'])) {
                $produto->marcas()->sync($validatedData['marcas']);
            } else {
                $produto->marcas()->sync([]);
            }
            if (isset($validatedData['cnpj_comprador'])) {
                $produto->cnpj_comprador = $validatedData['cnpj_comprador'];
                $produto->save();
            } 
            $retorno = ['message' => 'Produto atualizado com sucesso!', 'produto' => $produto];
        }
        return response()->json($retorno);
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            return response()->json(['message' => 'Ação não autorizada.'], 403);
        }

        $produto = Produto::find($request->id);
        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado.'], 404);
        }

        $produto->update(['status' => 'Inativo']);

        return response()->json(['message' => 'Produto excluído com sucesso!']);
    }

    /**
     * Get marcas related to a product by product id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMarcasByProduto(Request $request)
    {
        $produtoId = $request->id_produto;
        if (!$produtoId) {
            return response()->json(['error' => 'Produto ID is required'], 400);
        }

        $produto = Produto::with('marcas')->find($produtoId);
        if (!$produto) {
            return response()->json(['error' => 'Produto not found'], 404);
        }

        $marcas = $produto->marcas()->where('ativo', 1)->orderBy('nome')->get();

        if ($marcas->isEmpty()) {
            $semMarca = Marca::where('nome', 'Sem Marca')->first();
            if (!$semMarca) {
                // If "Sem Marca" does not exist in DB, create a dummy Marca object
                $semMarca = new Marca();
                $semMarca->id = 0;
                $semMarca->nome = 'Sem Marca';
                $semMarca->ativo = 1;
            }
            return response()->json(collect([$semMarca]));
        }

        return response()->json($marcas);
    }

    /**
     * Get active products list for select.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveProducts()
    {
        $produtos = Produto::where('status', 'Ativo')->orderBy('name')->get(['id', 'name']);
        return response()->json($produtos);
    }

    /**
     * Filter products by name for select input.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterProducts(Request $request)
    {
        $search = $request->query('search', '');

        $query = Produto::query()
            ->where('status', 'Ativo');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $produtos = $query->orderBy('name')->get(['id', 'name']);

        return response()->json($produtos);
    }
}
