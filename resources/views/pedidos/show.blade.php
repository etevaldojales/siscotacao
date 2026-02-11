@extends('adminlte::page')

@section('content')
<div class="container mt-4">
    <h3>Visualizar Pedido #{{ $pedido->numero }}</h3>

    <div class="card mb-4">
        <div class="card-header">
            Dados do Pedido
        </div>
        <div class="card-body">
            <p><strong>ID:</strong> {{ $pedido->id }}</p>
            <p><strong>Usuário:</strong> {{ $pedido->usuario->name ?? 'N/A' }}</p>
            <p><strong>Fornecedor:</strong> {{ $pedido->fornecedor->razao_social ?? 'N/A' }}</p>
            <p><strong>Valor:</strong> R$ {{ number_format($pedido->valor, 2, ',', '.') }}</p>
            <p><strong>Status:</strong> 
                @switch($pedido->status)
                    @case(1) Gerado @break
                    @case(2) Enviado @break
                    @case(3) Cancelado @break
                    @case(4) Recebido @break
                    @case(5) Aprovado @break
                    @default Desconhecido
                @endswitch
            </p>
            <p><strong>Forma de Pagamento:</strong> 
                @switch($pedido->forma_pagamento)
                    @case(1) À vista @break
                    @case(2) 30 dias @break
                    @case(3) 60 dias @break
                    @default Desconhecida
                @endswitch
            </p>
            <p><strong>Prazo de Entrega:</strong> {{ $pedido->prazo_entrega ?? '-' }}</p>
            <p><strong>Tipo Frete:</strong> 
                @switch($pedido->tipo_frete)
                    @case(1) CIF @break
                    @case(2) FOB @break
                    @default Desconhecido
                @endswitch
            </p>
            <p><strong>Valor Frete:</strong> R$ {{ number_format($pedido->valor_frete ?? 0, 2, ',', '.') }}</p>
            <p><strong>Observação:</strong> {{ $pedido->observacao ?? '-' }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Itens do Pedido
        </div>
        <div class="card-body p-0">
            @if($result->isEmpty())
                <p class="p-3">Nenhum item encontrado para este pedido.</p>
            @else
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Produto</th>
                        <th>Marca</th>
                        <th>Quantidade</th>
                        <th>Unidade</th>
                        <th>Valor Unitário</th>
                        <th>Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($result as $item)
                    <tr>
                        <td>{{ $item['id'] }}</td>
                        <td>{{ $item['produto_nome'] ?? 'N/A' }}</td>
                        <td>{{ $item['marca_nome'] ?? 'N/A' }}</td>
                        <td>{{ $item['quantidade'] }}</td>
                        <td>
                            @switch($item['unidade'])
                                @case(1) Unidade @break
                                @case(2) Kg @break
                                @case(3) Litro @break
                                @case(4) Metro @break
                                @case(5) Pacote @break
                                @default Desconhecida
                            @endswitch
                        </td>
                        <td>R$ {{ number_format($item['valor_unitario'], 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($item['valor_total'], 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    <a href="{{ route('pedidos.index') }}" class="btn btn-secondary mt-3">Voltar</a>
</div>
@endsection
