<?php

namespace App\Http\Controllers;

use App\Models\Cotacao;
use Illuminate\Http\Request;

use App\Models\ItensCotacao;
use App\Models\FornecedorCotacao;
use App\Models\Fornecedor;
use App\Models\Marca;
use App\Models\Produto;

use Illuminate\Support\Facades\Mail;
use App\Mail\CotacaoFornecedorMail;
use App\Services\PDFService;
use Symfony\Component\HttpFoundation\Response;

use Carbon\Carbon;

class CotacaoController extends Controller
{
    protected $pdfService;

    public function __construct(PDFService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function verificarStatusCotacoes()
    {
        $nowTimestamp = Carbon::now()->timestamp;

        $cotacoes = Cotacao::where('status', 1)->get();

        $idsToUpdate = $cotacoes->filter(function ($cotacao) use ($nowTimestamp) {
            return $cotacao->encerramento->timestamp <= $nowTimestamp;
        })->pluck('id');

        if ($idsToUpdate->isNotEmpty()) {
            Cotacao::whereIn('id', $idsToUpdate)->update(['status' => 3]);
        }
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user && $user->isAdmin()) {
            $cotacoes = Cotacao::orderBy('id', 'desc')->paginate(10)->withQueryString();
            $produtos = \App\Models\Produto::all();
        } else {
            $cotacoes = Cotacao::where('id_usuario', $user->getAuthIdentifier())->orderBy('id', 'desc')->paginate(10)->withQueryString();
            $produtos = Produto::where('cnpj_comprador', $user->cnpj)->get();
        }

        if ($request->ajax()) {
            return response()->json([
                'table' => view('cotacao.partials.cotacao_table', compact('cotacoes'))->render(),
                'pagination' => view('cotacao.partials.cotacao_pagination', compact('cotacoes'))->render(),
            ]);
        }

        return view('cotacao.index', compact('cotacoes', 'produtos'));
    }

    /**
     * Generate and return PDF for a given Cotacao.
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generatePDF($id)
    {
        $cotacao = Cotacao::findOrFail($id);
        $pdf = $this->pdfService->generatePDF($cotacao);

        return $pdf->stream("cotacao_{$cotacao->numero}.pdf");
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $user = auth()->user();
        if ($user && $user->isAdmin()) {
            $cotacoes = Cotacao::where(function ($q) use ($query) {
                $q->where('numero', 'LIKE', "%{$query}%")
                    ->orWhere('status', 'LIKE', "%{$query}%")
                    ->orWhere('status_envio', 'LIKE', "%{$query}%");
            })->orderBy('id', 'desc')->paginate(10)->withQueryString();
        } else {
            $cotacoes = Cotacao::where('id_usuario', $user->id)
                ->where(function ($q) use ($query) {
                    $q->where('numero', 'LIKE', "%{$query}%")
                        ->orWhere('status', 'LIKE', "%{$query}%")
                        ->orWhere('status_envio', 'LIKE', "%{$query}%");
                })->orderBy('id', 'desc')->paginate(10)->withQueryString();
        }

        if ($request->ajax()) {
            return response()->json([
                'table' => view('cotacao.partials.cotacao_table', compact('cotacoes'))->render(),
                'pagination' => view('cotacao.partials.cotacao_pagination', compact('cotacoes'))->render(),
            ]);
        }

        return view('cotacao.index', compact('cotacoes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|numeric|unique:cotacoes,numero,' . $request->id,
            'inicio' => 'nullable|date',
            'encerramento' => 'required|date',
            'status' => 'required|integer|in:1,2,3,4,5',
            'status_envio' => 'required|integer|in:1,2',
            'descricao' => 'nullable|string',
            'observacao' => 'nullable|string',
            'endereco_entrega' => 'nullable|string',
        ]);

        $userId = auth()->id();

        if ($request->id == 0) {
            $cotacao = new Cotacao($request->all());
            $cotacao->id_usuario = $userId;
            $cotacao->save();
        } elseif ($request->id > 0) {
            $cotacao = Cotacao::find($request->id);
            $cotacao->fill($request->all());
            $cotacao->id_usuario = $userId;
            $cotacao->save();
        }

        return response()->json($cotacao);
    }

    public function get(Request $request)
    {
        $cotacao = Cotacao::findOrFail($request->id);
        return response()->json($cotacao);
    }

    public function destroy(Request $request)
    {
        $cotacao = Cotacao::findOrFail($request->id);
        // Soft delete by setting status to 4 (Cancelado)
        $cotacao->status = 4;
        $cotacao->save();

        return response()->json(['success' => true]);
    }

    // Itens Cotacao methods

    public function listItens(Request $request)
    {
        $cotacaoId = $request->input('cotacao_id');
        $itens = ItensCotacao::with(['produto', 'marca'])->where('cotacao_id', $cotacaoId)->get();

        $result = $itens->map(function ($item) {
            return [
                'id' => $item->id,
                'cotacao_id' => $item->cotacao_id,
                'product_id' => $item->product_id,
                'marca_id' => $item->marca_id,
                'quantidade' => $item->quantidade,
                'unidade' => $item->unidade,
                'valor' => $item->valor,
                'observacao' => $item->observacao,
                'produto_nome' => $item->produto ? $item->produto->name : null,
                'marca_nome' => $item->marca ? $item->marca->nome : null,
            ];
        });

        return response()->json($result);
    }

    public function storeItem(Request $request)
    {
        $request->validate([
            'cotacao_id' => 'required|exists:cotacoes,id',
            'product_id' => 'required|exists:products,id',
            'marca_id' => 'required|exists:marcas,id',
            'quantidade' => 'required|integer',
            'unidade' => 'required|integer|in:1,2,3,4,5', // 1: kg, 2: cx, 3: unid, 4: saco, 5: metro
            'valor' => 'required|numeric',
            'observacao' => 'nullable|string',
        ]);

        if ($request->id == 0) {
            $item = new ItensCotacao($request->all());
            $item->save();
        } else {
            $item = ItensCotacao::find($request->id);
            $item->fill($request->all());
            $item->save();
        }

        //return response()->json($item);
        return response()->json($request->all());
    }

    public function getItem(Request $request)
    {
        $item = ItensCotacao::findOrFail($request->id);
        return response()->json($item);
    }

    public function destroyItem(Request $request)
    {
        $item = ItensCotacao::findOrFail($request->id);
        $item->delete();

        return response()->json(['success' => true]);
    }

    // FornecedorCotacao methods

    public function listItensCotacao($id, $id_fornec)
    {
        $this->verificarStatusCotacoes();
        $cotacaoId = $id;
        $cotacao = Cotacao::findOrFail($cotacaoId);
        $fornecedor = Fornecedor::findOrFail($id_fornec);
        $itens = ItensCotacao::with(['produto', 'marca'])->where('cotacao_id', $cotacaoId)->get();

        $itens_cotacao = $itens->map(function ($item) {
            return [
                'id' => $item->id,
                'cotacao_id' => $item->cotacao_id,
                'product_id' => $item->product_id,
                'marca_id' => $item->marca_id,
                'quantidade' => $item->quantidade,
                'observacao' => $item->observacao,
                'unidade' => $this->getUnidadeItem($item->unidade),
                'valor' => $item->valor,
                'produto_nome' => $item->produto ? $item->produto->name : null,
                'marca_nome' => $item->marca ? $item->marca->nome : null,
            ];
        });

        return view('cotacao.fornecedor_cotacao', compact('itens_cotacao', 'cotacao', 'fornecedor'));
    }

    public function getUnidadeItem($v)
    {
        switch ($v) {
            case 1:
                return 'Kg';
            case 2:
                return 'Cx';
            case 3:
                return 'Unid';
            case 4:
                return 'Saco';
            case 5:
                return 'Metro';
        }
    }

    public function listFornecedorCotacao(Request $request)
    {
        $itemId = $request->input('item_id');
        $fornecedoresCotacao = FornecedorCotacao::with(['fornecedor'])->where('item_id', $itemId)->where('valor_unitario', '>', 0)->orderBy('valor_unitario', 'asc')->get();

        $result = $fornecedoresCotacao->map(function ($fc) {
            return [
                'id' => $fc->id,
                'cotacao_id' => $fc->cotacao_id,
                'item_id' => $fc->item_id,
                'marca' => $this->getMarca($fc->cotacao_id, $fc->item_id),
                'fornecedor_id' => $fc->fornecedor_id,
                'fornecedor_nome' => $fc->fornecedor ? $fc->fornecedor->razao_social : null,
                'valor_unitario' => $fc->valor_unitario,
                'valor_total' => $fc->valor_total,
                'st_aprovado' => $fc->staprovado,
                'tipo_frete' => $fc->tipo_frete,
                'valor_frete' => $fc->valor_frete,
                'faturamento_minimo' => $fc->faturamento_minimo,
                'observacao' => $fc->observacao,
                'forma_pagamento' => $fc->forma_pagamento,
                'prazo_entrega' => $fc->prazo_entrega
            ];
        });

        return response()->json($result);
    }

    public function aprovarItem(Request $request)
    {
        try {
            //\Log::info('Request: ', [$request]);
            
            $fc = FornecedorCotacao::where('cotacao_id', '=', $request->cotacao_id)->where('item_id', '=', $request->item_id)->get();
            
            if ($fc) {
                foreach ($fc as $f) {
                    $f->staprovado = 0;
                    $f->save();
                }
            }
            $fcAt = FornecedorCotacao::findOrFail($request->id);
            $fcAt->staprovado = $request->valor;
            $fcAt->save();

            // Save justificativa if provided
            if ($request->has('justificativa') && !empty($request->justificativa)) {
                $userId = auth()->id();
                $justificativa = new \App\Models\Justificativa();
                $justificativa->id_usuario = $userId;
                $justificativa->cotacao_id = $request->cotacao_id;
                $justificativa->item_id = $request->item_id;
                $justificativa->valor_unitario = $fcAt->valor_unitario;
                $justificativa->justificativa_id  = $request->justificativa;
                $justificativa->save();
            }

            return response()->json(1);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }


    public function getMarca($cotacao_id, $item_id)
    {
        $item = ItensCotacao::with('marca')->where('cotacao_id', $cotacao_id)->where('id', $item_id)->first();
        if ($item && $item->marca) {
            return $item->marca->nome;
        }
        return null;
    }

    public function storeFornecedorCotacao(Request $request)
    {

        $request->validate([
            'cotacao_id' => 'required|exists:cotacoes,id',
            'item_id' => 'required|exists:itens_cotacao,id',
            'fornecedor_id' => 'required|exists:fornecedores,id',
            'valor_unitario' => 'required|numeric',
            'valor_total' => 'required|numeric',
        ]);

        if ($request->id == 0) {
            $fc = new FornecedorCotacao($request->all());
            $fc->save();
        } else {
            $fc = FornecedorCotacao::find($request->id);
            $fc->fill($request->all());
            $fc->save();
        }

        return response()->json($fc);
    }

    public function getFornecedorCotacao(Request $request)
    {
        $fc = FornecedorCotacao::findOrFail($request->id);
        return response()->json($fc);
    }

    public function destroyFornecedorCotacao(Request $request)
    {
        $fc = FornecedorCotacao::findOrFail($request->id);
        $fc->delete();

        return response()->json(['success' => true]);
    }

    public function storeFornecedorCotacaoBatch(Request $request)
    {
        try {

            $data = $request->validate([
                'cotacao_id' => 'required|exists:cotacoes,id',
                'fornecedor_id' => 'required|exists:fornecedores,id',
                'forma_pagamento' => 'required|integer|in:1,2,3',
                'prazo_entrega' => 'nullable|string',
                'tipo_frete' => 'required|integer|in:1,2',
                'valor_frete' => 'nullable|numeric',
                'faturamento_minimo' => 'nullable|numeric',
                'items' => 'required|array',
                'items.*.id' => 'exists:itens_cotacao,id',
                'items.*.quantidade' => 'required|numeric',
                'items.*.valor_unitario' => 'required|numeric',
                'items.*.valor_total' => 'required|numeric',
                'items.*.observacao' => 'nullable|string',
            ]);

            $cotacao_id = $data['cotacao_id'];
            $fornecedor_id = $data['fornecedor_id'];
            $forma_pagamento = $data['forma_pagamento'];
            $prazo_entrega = $data['prazo_entrega'];
            $tipo_frete = $data['tipo_frete'];
            $valor_frete = $data['valor_frete'];
            $faturamento_minimo = $data['faturamento_minimo'];
            //$observacao = $data['observacao'];
            $items = $data['items'];

            $fcexist = FornecedorCotacao::where('cotacao_id', '=', $cotacao_id)->where('fornecedor_id', '=', $fornecedor_id)->get();
            if ($fcexist) {
                FornecedorCotacao::where('cotacao_id', $cotacao_id)
                    ->where('fornecedor_id', $fornecedor_id)
                    ->delete();
            }

            foreach ($items as $item) {
                $fc = new FornecedorCotacao();
                $fc->cotacao_id = $cotacao_id;
                $fc->fornecedor_id = $fornecedor_id;
                $fc->item_id = $item['id'];
                $fc->valor_unitario = $item['valor_unitario'];
                $fc->valor_total = $item['valor_total'];
                $fc->forma_pagamento = $forma_pagamento;
                $fc->prazo_entrega = $prazo_entrega;
                $fc->tipo_frete = $tipo_frete;
                $fc->valor_frete = $valor_frete;
                $fc->faturamento_minimo = $faturamento_minimo;
                $fc->observacao = $item['observacao'];
                $fc->save();
            }

            return response()->json(['success' => true, 'message' => 'Fornecedor Cotação itens salvos com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getItensCotacao(Request $request)
    {
        $cotacaoId = $request->input('cotacaoId');
        $itens = ItensCotacao::with(['produto', 'marca'])->where('cotacao_id', $cotacaoId)->get();

        $result = $itens->map(function ($item) {
            return [
                'id' => $item->id,
                'cotacao_id' => $item->cotacao_id,
                'product_id' => $item->product_id,
                'marca_id' => $item->marca_id,
                'quantidade' => $item->quantidade,
                'unidade' => $this->getUnidadeItem($item->unidade),
                'valor' => $item->valor,
                'observacao' => $item->observacao,
                'produto_nome' => $item->produto ? $item->produto->name : null,
                'marca_nome' => $item->marca ? $item->marca->nome : null,
            ];
        });

        return response()->json($result);
    }

    public function sendCotacaoEmails(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:cotacoes,id',
            ]);

            $cotacaoId = $request->input('id');
            $cotacao = Cotacao::findOrFail($cotacaoId);

            // Get cotacao items with their categories
            $itens = ItensCotacao::with('produto.categoria')->where('cotacao_id', $cotacaoId)->get();

            if ($itens) {
                // Get unique category ids from cotacao items
                $categoryIds = $itens->pluck('produto.categoria.id')->unique()->filter();

                // Find fornecedores whose categories match cotacao item categories
                $fornecedores = Fornecedor::whereHas('categorias', function ($query) use ($categoryIds) {
                    $query->whereIn('categoria_id', $categoryIds);
                })->get();

                $cotacao->status_envio = 2; // Status cotação enviada
                $cotacao->save();

                // Send email to each fornecedor
                $msg = 'Falha ao enviar emails';
                $link = "";
                foreach ($fornecedores as $fornecedor) {
                    //$path = "https://abastecejacompras.com.br/siscotacao/public";
                    $path = "";
                    $link = url($path . "/cotacao/fornecedor/{$cotacaoId}/{$fornecedor->id}");
                    Mail::to($fornecedor->email)->send(new CotacaoFornecedorMail($link, $fornecedor->razao_social));
                    $msg = 'Emails enviados com sucesso';
                }

                return response()->json(['success' => true, 'message' => $msg]);

            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

}
