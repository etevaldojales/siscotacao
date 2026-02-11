<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Cotacao;
use App\Models\FornecedorCotacao;
use App\Models\Pedido;
use App\Mail\ConfirmacaoCotacaoFornecedorMail;
use App\Mail\PedidoFornecedorMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Services\PDFService;
use App\Http\Controllers\CotacaoController;
use Carbon\Carbon;
use App\Models\ItensCotacao;
use App\Models\Fornecedor;
use App\Mail\CotacaoFornecedorMail;



class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['confirmarPedido']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $this->verificarStatusCotacoes();
        $this->verificarCotacoesProgramadas();
        $user = auth()->user();

        if ($user && $user->isAdmin()) {
            $menus = Menu::where('actived', true)->get();
        } else {
            $menus = Menu::join('role_menu', 'menu.id', '=', 'role_menu.menu_id')
                ->where('role_menu.role_id', 2)
                ->where('menu.actived', true)
                ->select('menu.*')
                ->get();
        }
        //dd($menus);
        return view('home', compact('menus'));
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
    
    public function verificarCotacoesProgramadas()
    {
        // Get current date and time
        $now = Carbon::now();

        // Find cotacoes with status in (1, 2), status_envio=1, and inicio less than or equal to now
        $cotacoes = Cotacao::whereIn('status', [1, 2])
            ->where('status_envio', '=', 1)
            ->where('inicio', '<=', $now)
            ->orderBy('inicio', 'desc')
            ->get();

        \Log::info('verificarCotacoesProgramadas started', ['count' => $cotacoes->count()]);

        if ($cotacoes->isNotEmpty()) {
            foreach ($cotacoes as $cotacao) {
                try {
                    $this->sendCotacaoEmails($cotacao->id);
                    \Log::info('Email enviado para cotacao', ['cotacao_id' => $cotacao->id]);
                } catch (\Exception $e) {
                    \Log::error('Falha ao enviar email para cotacao', ['cotacao_id' => $cotacao->id, 'error' => $e->getMessage()]);
                }
            }
        } else {
            \Log::info('Nenhuma cotação programada encontrada');
        }
    }

    public function getActiveCotacoes()
    {
        $user = auth()->user();
        if ($user && $user->isAdmin()) {
            $cotacoes = Cotacao::whereIn('status', [1, 3])
                ->where('status_envio', 2)
                ->orderBy('id', 'desc')
                ->where(function ($query) {
                    $query->where('status', 1)
                        ->orWhere(function ($query) {
                            $query->where('status', 3)
                                ->whereExists(function ($query) {
                                    $query->select(\DB::raw(1))
                                        ->from('fornecedor_cotacao')
                                        ->whereRaw('fornecedor_cotacao.cotacao_id = cotacoes.id');
                                });
                        });
                })
                ->get(['id', 'numero', 'encerramento', 'status']);
        } else {
            $cotacoes = Cotacao::whereIn('status', [1, 3])
                ->where('id_usuario', '=', $user->id)
                ->where('status_envio', 2)
                ->orderBy('id', 'desc')
                ->where(function ($query) {
                    $query->where('status', 1)
                        ->orWhere(function ($query) {
                            $query->where('status', 3)
                                ->whereExists(function ($query) {
                                    $query->select(\DB::raw(1))
                                        ->from('fornecedor_cotacao')
                                        ->whereRaw('fornecedor_cotacao.cotacao_id = cotacoes.id');
                                });
                        });
                })
                ->get(['id', 'numero', 'encerramento', 'status']);
        }


        return response()->json($cotacoes);
    }

    public function getFornecedorCotacoes(Request $request)
    {
        $fornecedorCotacoes = FornecedorCotacao::selectRaw('fornecedor_id, cotacao_id, SUM(valor_total) as total_valor, fornecedores.razao_social')
            ->join('fornecedores', 'fornecedores.id', '=', 'fornecedor_cotacao.fornecedor_id')
            ->where('cotacao_id', $request->cotacaoId)
            ->groupBy('fornecedor_id', 'cotacao_id', 'fornecedores.razao_social')
            ->get();

        return response()->json($fornecedorCotacoes);
    }

    public function getFornecedorDetails(Request $request)
    {
        $fornecedor = \App\Models\Fornecedor::findOrFail($request->fornecedorId);

        $fornecedorCotacoes = FornecedorCotacao::with('item.produto')
            ->where('cotacao_id', $request->cotacaoId)
            ->where('fornecedor_id', $request->fornecedorId)
            ->get()
            ->map(function ($fc) {
                return [
                    'id' => $fc->id,
                    'cotacao_id' => $fc->cotacao_id,
                    'fornecedor_id' => $fc->fornecedor_id,
                    'valor_unitario' => $fc->valor_unitario,
                    'valor_total' => $fc->valor_total,
                    'product_name' => $fc->item ? ($fc->item->produto ? $fc->item->produto->name : null) : null,
                    'itens_cotacao_quantidade' => $fc->item ? $fc->item->quantidade : null,
                    'forma_pagamento' => $fc->forma_pagamento,
                    'prazo_entrega' => $fc->prazo_entrega
                ];
            });

        return response()->json([
            'fornecedor' => $fornecedor,
            'fornecedor_cotacoes' => $fornecedorCotacoes,
        ]);
    }


    public function menu($id)
    {
        $menu = Menu::where('actived', true)->where('id', $id)->firstOrFail();
        //dd($menu);
        return view('menu', compact('menu'));
    }

    public function aprovarCotacao(Request $request)
    {
        $request->validate([
            'cotacao_id' => 'required|integer|exists:cotacoes,id',
        ]);

        $cotacaoId = $request->input('cotacao_id');

        // Update cotacao status to 6 (Aprovado)
        $cotacao = Cotacao::find($cotacaoId);
        if (!$cotacao) {
            return response()->json(['success' => false, 'message' => 'Cotação não encontrada.'], 404);
        }
        $cotacao->status = 6;
        $cotacao->save();

        // Get all fornecedores with staprovado = 1 for this cotacao
        $fornecedores = FornecedorCotacao::where('cotacao_id', $cotacaoId)
            ->where('staprovado', 1)
            ->with(['fornecedor', 'item.produto', 'item.marca'])
            ->get()
            ->groupBy('fornecedor_id');

        if ($fornecedores->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Nenhum fornecedor aprovado encontrado para esta cotação.'], 404);
        }

        foreach ($fornecedores as $fornecedorId => $fornecedorCotacoes) {
            // Build cotacao data for PDF
            $cotacaoData = new \stdClass();
            $cotacaoData->numero = $cotacao->numero;
            $cotacaoData->descricao = $cotacao->descricao;
            $cotacaoData->observacao = $cotacao->observacao;
            $cotacaoData->endereco_entrega = $cotacao->endereco_entrega;
            $cotacaoData->valor = $fornecedorCotacoes->sum('valor_total');
            $cotacaoData->fornecedor = $fornecedorCotacoes->first()->fornecedor;
            $cotacaoData->itens = $fornecedorCotacoes->map(function ($fc) {
                $item = $fc->item;
                $produto = $item->produto ?? null;
                $marca = $item->marca ?? null;
                return (object) [
                    'produto' => $produto,
                    'marca' => $marca,
                    'quantidade' => $item->quantidade ?? null,
                    'peso' => $item->peso ?? null,
                    'unidade' => $item->unidade ?? null,
                    'valor_unitario' => $fc->valor_unitario,
                    'valor_total' => $fc->valor_total,
                ];
            });

            // Generate PDF from blade template
            //$pdf = \PDF::loadView('cotacao.pdf', ['cotacao' => $cotacaoData])->output();

            $pdfService = new PDFService();
            $pdf = $pdfService->generateCotacaoPDF($cotacaoData);

            // Generate PDF content as string
            $pdfContent = $pdf->output();

            // Generate confirmation link
            //$confirmationLink = URL::signedRoute('pedido.confirmar', ['cotacao_id' => $cotacaoId, 'fornecedor_id' => $fornecedorId]);
            $confirmationLink = url("/pedido/confirmar/{$cotacaoId}/{$fornecedorId}");
            // Send email with PDF attached
            $mail = new ConfirmacaoCotacaoFornecedorMail($confirmationLink, $cotacaoData->fornecedor->razao_social, $pdfContent);
            Mail::to($cotacaoData->fornecedor->email)->send($mail);
        }

        return response()->json(['success' => true, 'message' => 'Cotação aprovada e emails enviados aos fornecedores aprovados.']);
    }

    public function sendCotacaoEmails($id)
    {
        try {


            $cotacaoId = $id;
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
                return true;
            }

        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function confirmarPedido($cotacaoId, $fornecedorId)
    {
        try {

            // Check if pedido already exists for this cotacao and fornecedor
            $existingPedido = Pedido::where('cotacao_id', $cotacaoId)
                ->where('id_fornecedor', $fornecedorId)
                ->first();

            if ($existingPedido) {
                return view('pedidos.confirmar', ['success' => false, 'message' => 'Pedido já foi gerado para esta cotação e fornecedor.']);
            }

            $cotacao = Cotacao::findOrFail($cotacaoId);
            $fornecedorCotacao = FornecedorCotacao::where('cotacao_id', $cotacaoId)
                ->where('fornecedor_id', $fornecedorId)
                ->where('staprovado', '=', 1)
                ->first();

            if (!$fornecedorCotacao) {
                return view('pedidos.confirmar', ['success' => false, 'message' => 'Fornecedor Cotação não encontrada.']);
            }
            //dd($fornecedorCotacao);

            // 1. Generate Pedido based on fornecedor_cotacao
            $pedido = new Pedido();
            $pedido->id_usuario = $cotacao->id_usuario;
            $pedido->id_fornecedor = $fornecedorCotacao->fornecedor_id;
            $pedido->cotacao_id = $cotacaoId;
            $pedido->valor = 0;
            $pedido->actived = 1;
            $pedido->numero = date('YmdHis') . 1; // Generate a unique order number, can be improved
            $pedido->status = 5; // Assuming 5 pedido Aprovado pelo Fornecedor
            $pedido->forma_pagamento = 1;
            $pedido->prazo_entrega = "";
            $pedido->tipo_frete = 1;
            $pedido->valor_frete = 0;
            $pedido->observacao = "";

            $pedido->save();

            // 2. Populate pedido_items with data from fornecedor_cotacao approved
            $fornecedorCotacoes = FornecedorCotacao::select('fornecedor_cotacao.*', 'itens_cotacao.quantidade', 'itens_cotacao.marca_id', 'itens_cotacao.product_id', 'itens_cotacao.unidade')
                ->join('itens_cotacao', 'fornecedor_cotacao.item_id', '=', 'itens_cotacao.id')
                ->where('fornecedor_cotacao.cotacao_id', $fornecedorCotacao->cotacao_id)
                ->where('fornecedor_cotacao.fornecedor_id', $fornecedorCotacao->fornecedor_id)
                ->where('fornecedor_cotacao.staprovado', '=', 1)
                ->get();

            foreach ($fornecedorCotacoes as $fc) {
                if ($fc) {
                    $pedidoItem = new \App\Models\PedidoItem();
                    $pedidoItem->pedido_id = $pedido->id;
                    $pedidoItem->product_id = $fc->product_id;
                    $pedidoItem->marca_id = $fc->marca_id;
                    $pedidoItem->quantidade = $fc->quantidade;
                    $pedidoItem->unidade = $fc->unidade;
                    $pedidoItem->valor_unitario = $fc->valor_unitario;
                    $pedidoItem->valor_total = $fc->valor_total;
                    $pedidoItem->observacao = $fc->observacao;
                    $pedidoItem->save();
                    $pedido->forma_pagamento = $fc->forma_pagamento;
                    $pedido->prazo_entrega = $fc->prazo_entrega;
                    $pedido->tipo_frete = $fc->tipo_frete;
                    $pedido->valor_frete = $fc->valor_frete;
                    $pedido->save();
                }
            }
            // Altera valor do pedido com o somatório dos itens do pedido gerado

            $pedido->valor = \App\Models\PedidoItem::where('pedido_id', $pedido->id)->sum('valor_total');
            $pedido->save();

            // Atualiza o status da cotação para 6 (Aprovada);
            $cotacao->status = 6;
            $cotacao->save();

            // 3. Generate PDF of pedido and send to fornecedor
            $pdfService = new PDFService();
            $pdf = $pdfService->generatePedidoPDF($pedido)->output();

            $mail = new PedidoFornecedorMail($pedido, $pdf);
            Mail::to($fornecedorCotacao->fornecedor->email)->send($mail);

            return view('pedidos.confirmar', ['success' => true, 'message' => 'Pedido confirmado com sucesso.']);

        } catch (\Exception $e) {
            return view('pedidos.confirmar', ['success' => false, 'message' => 'Erro ao gerar pedido.' . $e->getMessage()]);
        }

    }


}
