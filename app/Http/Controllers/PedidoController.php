<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\PDFController;
use App\Services\PDFService;


class PedidoController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (request()->ajax()) {

            if ($user && $user->isAdmin()) {
                $pedidos = Pedido::with(['usuario', 'fornecedor'])->get();
            } else {
                $pedidos = Pedido::with(['usuario', 'fornecedor'])->where('id_usuario', '=', $user->id)->get();
            }

            return response()->json($pedidos);
        }
        // Return view for non-AJAX requests
        if ($user && $user->isAdmin()) {
            $pedidos = Pedido::with(['usuario', 'fornecedor'])->get();
        } else {
            $pedidos = Pedido::with(['usuario', 'fornecedor'])->where('id_usuario', '=', $user->id)->get();
        }

        //dd($pedidos);
        return view('pedidos.index', compact('pedidos'));
    }

    public function produtosComprador()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado'], 401);
        }

        $produtos = $user->produtosComprador()->with('marcas')->get();

        return response()->json($produtos);
    }

    public function get(Request $request)
    {
        $id = $request->id;
        $pedido = Pedido::find($id);
        return response()->json($pedido);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:users,id',
            'id_fornecedor' => 'required|exists:fornecedores,id',
            'num_pedido' => 'required|numeric',
            'valor' => 'required|numeric',
            'actived' => 'required|boolean',
            'status' => 'required|integer|in:1,2,3,4',
            'forma_pagamento' => 'required|integer|in:1,2,3',
            'prazo_entrega' => 'nullable|string',
            'tipo_frete' => 'required|integer|in:1,2',
            'valor_frete' => 'nullable|numeric',
            'observacao' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Generate numero as ymdHis.id_usuario
        $numero = $request->num_pedido;
        $pedido = Pedido::where('numero', '=', $numero);
        if ($pedido->exists()) {
            $pedido->id_usuario = $request->id_usuario;
            $pedido->id_fornecedor = $request->id_fornecedor;
            $pedido->numero = $numero;
            $pedido->valor = $request->valor;
            $pedido->actived = $request->actived;
            $pedido->status = $request->status;
            $pedido->forma_pagamento = $request->forma_pagamento;
            $pedido->prazo_entrega = $request->prazo_entrega;
            $pedido->tipo_frete = $request->tipo_frete;
            $pedido->valor_frete = $request->valor_frete;
            $pedido->observacao = $request->observacao;
            $pedido->save();
        } else {
            $pedido = Pedido::create([
                'id_usuario' => $request->id_usuario,
                'id_fornecedor' => $request->id_fornecedor,
                'numero' => $numero,
                'valor' => $request->valor,
                'actived' => $request->actived,
                'status' => $request->status,
                'forma_pagamento' => $request->forma_pagamento,
                'prazo_entrega' => $request->prazo_entrega,
                'tipo_frete' => $request->tipo_frete,
                'valor_frete' => $request->valor_frete,
                'observacao' => $request->observacao,

            ]);
        }

        return response()->json(['message' => 'Pedido salvo com sucesso', 'pedido' => $pedido->load('itens')], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $pedido = Pedido::find($id);
        if (!$pedido) {
            return response()->json(['message' => 'Pedido não encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|integer|in:1,2,3,4',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $pedido->status = $request->status;
        $pedido->save();

        if ($pedido->status == 2) { // Gera um pdf e envia o pedido para o email do fornecedor 
            // Use PDFController to generate PDF
            $pdfService = new PDFService();
            $pdf = $pdfService->generatePedidoPDF($pedido);

            // Generate PDF content as string
            $pdfContent = $pdf->output();

            // Send email to fornecedor with PDF attachment
            \Mail::to($pedido->fornecedor->email)->send(new \App\Mail\PedidoFornecedorMail($pedido, $pdfContent));
        }

        return response()->json(['message' => 'Status alterado com sucesso. Pedido enviado', 'pedido' => $pedido]);
    }

    public function update(Request $request, $id)
    {
        $pedido = Pedido::find($id);
        if (!$pedido) {
            return response()->json(['message' => 'Pedido não encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:users,id',
            'id_fornecedor' => 'required|exists:fornecedores,id',
            'num_pedido' => 'required|numeric',
            'valor' => 'required|numeric',
            'actived' => 'required|boolean',
            'status' => 'required|integer|in:1,2,3,4',
            'forma_pagamento' => 'required|integer|in:1,2,3',
            'prazo_entrega' => 'nullable|string',
            'tipo_frete' => 'required|integer|in:1,2',
            'valor_frete' => 'nullable|numeric',
            'observacao' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $pedido->id_usuario = $request->id_usuario;
        $pedido->id_fornecedor = $request->id_fornecedor;
        $pedido->numero = $request->num_pedido;
        $pedido->valor = $request->valor;
        $pedido->actived = $request->actived;
        $pedido->status = $request->status;
        $pedido->forma_pagamento = $request->forma_pagamento;
        $pedido->prazo_entrega = $request->prazo_entrega;
        $pedido->tipo_frete = $request->tipo_frete;
        $pedido->valor_frete = $request->valor_frete;
        $pedido->observacao = $request->observacao;
        $pedido->save();

        return response()->json(['message' => 'Pedido atualizado com sucesso', 'pedido' => $pedido]);
    }

    // Pedido Items methods

    public function listItensPedido(Request $request)
    {
        $pedidoId = $request->pedido_id;
        $itens = PedidoItem::with(['produto', 'marca'])->where('pedido_id', $pedidoId)->get();

        $result = $itens->map(function ($item) {
            return [
                'id' => $item->id,
                'pedido_id' => $item->pedido_id,
                'product_id' => $item->product_id,
                'marca_id' => $item->marca_id,
                'quantidade' => $item->quantidade,
                'unidade' => $item->unidade,
                'valor_unitario' => $item->valor,
                'valor_total' => $item->valor_total,
                'produto_nome' => $item->produto ? $item->produto->name : null,
                'marca_nome' => $item->marca ? $item->marca->nome : null,
            ];
        });

        return response()->json($result);
    }

    public function storeItemPedido(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'product_id' => 'required|exists:products,id',
            'marca_id' => 'required|exists:marcas,id',
            'quantidade' => 'required|integer',
            'unidade' => 'required|integer|in:1,2,3,4,5',
            'valor_unitario' => 'required|numeric',
            'valor_total' => 'required|numeric',
        ]);

        $data = $request->all();
        //$data['valor'] = $data['valor_unitario'];
        //$data['valor_total'] = $data['valor_total'];

        // Remove peso from data
        unset($data['peso']);

        if ($request->id == 0) {
            $item = new PedidoItem($data);
            $item->save();
        } else {
            $item = PedidoItem::find($request->id);
            $item->fill($data);
            $item->save();
        }

        // Recalculate pedido total valor by summing all item valor_totals
        $pedido = Pedido::find($request->pedido_id);
        $pedido->valor = PedidoItem::where('pedido_id', $pedido->id)->sum('valor_total');
        $pedido->save();

        return response()->json($item);
    }

    public function getItemPedido(Request $request)
    {
        $item = PedidoItem::findOrFail($request->id);
        return response()->json($item);
    }

    public function destroyItemPedido(Request $request)
    {
        $item = PedidoItem::findOrFail($request->id);
        $pedido = Pedido::find($item->pedido_id);
        $pedido->valor -= $item->valor_total;
        $pedido->save();
        $item->delete();

        return response()->json(['success' => true]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query', '');
        $user = auth()->user();

        $pedidosQuery = Pedido::with(['usuario', 'fornecedor']);

        if ($user && !$user->isAdmin()) {
            $pedidosQuery->where('id_usuario', '=', $user->id);
        }

        if (!empty($query)) {
            $pedidosQuery->where(function ($q) use ($query) {
                $q->where('numero', 'like', '%' . $query . '%')
                    ->orWhereHas('fornecedor', function ($q2) use ($query) {
                        $q2->where('razao_social', 'like', '%' . $query . '%');
                    })
                    ->orWhereHas('usuario', function ($q3) use ($query) {
                        $q3->where('name', 'like', '%' . $query . '%');
                    });
            });
        }

        $pedidos = $pedidosQuery->get();

        return response()->json($pedidos);
    }

    public function show($id)
    {
        $pedido = Pedido::with(['usuario', 'fornecedor'])->findOrFail($id);
        $itens = PedidoItem::with(['produto', 'marca'])->where('pedido_id', $id)->get();

        $result = $itens->map(function ($item) {
            return [
                'id' => $item->id,
                'pedido_id' => $item->pedido_id,
                'product_id' => $item->product_id,
                'marca_id' => $item->marca_id,
                'quantidade' => $item->quantidade,
                'unidade' => $item->unidade,
                'valor_unitario' => $item->valor_unitario,
                'valor_total' => $item->valor_total,
                'produto_nome' => $item->produto ? $item->produto->name : null,
                'marca_nome' => $item->marca ? $item->marca->nome : null,
            ];
        });

        //dd($result);
        return view('pedidos.show', compact('pedido', 'result'));
    }
}
