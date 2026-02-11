<!DOCTYPE html>
<html>
<head>
    <title>SisCotação - Confirmação de Pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        @if ($success)
            <div class="alert alert-success" role="alert">
                {{ $message }}
            </div>
        @else
            <div class="alert alert-danger" role="alert">
                {{ $message }}
            </div>
        @endif
        <a href="{{ url('/') }}" class="btn btn-primary mt-3">Voltar para o Início</a>
    </div>
</body>
</html>
