<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pedido #{{ $pedido->numero }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1, h2 { margin: 0; padding: 0; }
    </style>
</head>
<body>
    <?php 
    $formpg = "";
    if($pedido->forma_pagamento == 1) {
        $formpg = "A vista";
    }
    elseif($pedido->forma_pagamento == 2) {
        $formpg = "30 dias";
    }
    elseif($pedido->forma_pagamento == 2) {
        $formpg = "60 dias";
    }

    ?>
    <p style="text-align: center;"><h1>Abastece Já - SIS COTAÇÃO</h1></p>
    <h1>Pedido #{{ $pedido->numero }}</h1>
    <p><strong>Fornecedor:</strong> {{ $pedido->fornecedor->razao_social }}</p>
    <p><strong>Usuário:</strong> {{ $pedido->usuario->name }}</p>
    <p><strong>Valor Total:</strong> R$ {{ number_format($pedido->valor, 2, ',', '.') }}</p>
    <p><strong>Forma de Pagamento: </strong>{{ $formpg }}</p>
    <p><strong>Prazo de Entrega: </strong>{{ $pedido->prazo_entrega }}</p>
    <p><strong>Tipo de Frete: </strong>{{ $pedido->tipo_frete == 1 ? 'SIF' : 'FOB' }}</p>
    <p><strong>Valor do Frete: </strong>{{ $pedido->valor_frete > 0 ? number_format($pedido->valor_frete, 2 , ',', '.') : '' }}</p>
    <!--<p><strong>Observação: </strong>{{ $pedido->observacao }}</p>-->

    <h2>Itens do Pedido</h2>
    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Marca</th>
                <th>Quantidade</th>
                <th>Unidade</th>
                <th>Valor Unitário</th>
                <th>Valor Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->itens as $item)
            <tr>
                <td>{{ $item->produto->name ?? '' }}</td>
                <td>{{ $item->marca->nome ?? '' }}</td>
                <td>{{ $item->quantidade }}</td>
                <td>{{ \App\Helpers\Helper::getUnidadeItem($item->unidade) }}</td>
                <td>R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                <td>R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
