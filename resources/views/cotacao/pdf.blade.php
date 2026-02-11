<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cotação #{{ $cotacao->numero }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1, h2 { margin: 0; padding: 0; }
    </style>
</head>
<body>
    <p style="text-align: center;"><h1>Abastece Já - SIS COTAÇÃO</h1></p>
    <h1>Cotação #{{ $cotacao->numero }}</h1>
    <p><strong>Fornecedor:</strong> {{ $cotacao->fornecedor->razao_social }}</p>
    <p><strong>Descrição:</strong> {{ $cotacao->descricao }}</p>
    <p><strong>Observação:</strong> {{ $cotacao->observacao }}</p>
    <p><strong>Endereço de Entrega:</strong> {{ $cotacao->endereco_entrega }}</p>
    <p><strong>Valor Total:</strong> R$ {{ number_format($cotacao->valor, 2, ',', '.') }}</p>

    <h2>Itens da Cotação</h2>
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
            @foreach($cotacao->itens as $item)
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
