<!DOCTYPE html>
<html>
<head>
    <title>Nova Cotação</title>
</head>
<body>
    <p>Prezado fornecedor, {{ $nomeFornecedor }}</p>
    <p>Você recebeu uma nova cotação. Por favor, acesse o link abaixo para visualizar os detalhes:</p>
    <p><a href="{{ $link }}">{{ $link }}</a></p>
    <p>Atenciosamente,</p>
    <p>Equipe Siscotacao</p>
</body>
</html>
