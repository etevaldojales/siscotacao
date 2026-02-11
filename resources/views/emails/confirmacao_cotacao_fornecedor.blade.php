<!DOCTYPE html>
<html>
<head>
    <title>Cotação Aprovada</title>
</head>
<body>
    <p>Prezado fornecedor, {{ $nomeFornecedor }}</p>
    <p>Parabéns !! sua cotação foi aprovada. Por favor, acesse o link abaixo para confirmar o pedido:</p>
    <p><a href="{{ $link }}">{{ $link }}</a></p>
    <p>Atenciosamente,</p>
    <p>Equipe Siscotacao</p>
</body>
</html>
