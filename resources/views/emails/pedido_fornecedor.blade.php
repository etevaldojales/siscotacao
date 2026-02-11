<!DOCTYPE html>
<html>
<head>
    <title>Abasteceja - Pedido Enviado</title>
</head>
<body>
    <p>Olá {{ $pedido->fornecedor->razao_social }},</p>
    <p>Segue em anexo o pedido número {{ $pedido->numero }} para sua análise.</p>
    <p>Atenciosamente,</p>
    <p>Abastece Já Compras</p>
</body>
</html>
