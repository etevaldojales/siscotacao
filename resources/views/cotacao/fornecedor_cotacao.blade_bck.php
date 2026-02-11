<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SisCotação</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    <link rel="preload" as="style" href="https://sistema.abastecejacompras.com.br/build/assets/app-bd591852.css" />
    <link rel="modulepreload" href="https://sistema.abastecejacompras.com.br/build/assets/app-9114054a.js" />
    <link rel="stylesheet" href="https://sistema.abastecejacompras.com.br/build/assets/app-bd591852.css" />
    <script type="module" src="https://sistema.abastecejacompras.com.br/build/assets/app-9114054a.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <script>
        $(document).ready(function () {
            //$('#valor').mask('000.000.000.000.000,00', { reverse: true });

            // Prevent form submission on Enter key press
            $('#frmCotacaoFornec').on('keydown', function (e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    e.preventDefault();
                    return false;
                }
            });

            $('#btnEnvio').click(function (e) {
                e.preventDefault();

                // Validation
                var forma_pagamento = $('#forma_pagamento').val();
                var prazo_entrega = $('#prazo_entrega').val();
                var tipo_frete = $('#tipo_frete').val();
                var itensValidos = false;

                if (!forma_pagamento) {
                    alert('Por favor, selecione a Forma de Pagamento.');
                    return false;
                }

                if (!tipo_frete) {
                    alert('Por favor, selecione o Tipo de Frete.');
                    return false;
                }

                if (!prazo_entrega || prazo_entrega.trim() === '') {
                    alert('Por favor, informe o Prazo de Entrega.');
                    return false;
                }

                $('#itensCotacaoTable tbody tr').each(function () {
                    var quantidade = parseFloat($(this).find('td').eq(2).text()) || 0;
                    var valor_unitario = $(this).find("input[name='valor_unitario[]']").val();
                    valor_unitario = valor_unitario ? valor_unitario.replace(',', '.').trim() : '0';
                    valor_unitario = parseFloat(valor_unitario) || 0;

                    if (quantidade > 0 && valor_unitario > 0) {
                        itensValidos = true;
                        return false; // break loop
                    }
                });

                if (!itensValidos) {
                    alert('Por favor, informe pelo menos um item com quantidade e valor unitário válidos.');
                    return false;
                }

                $('#loading').show();
                var cotacao_id = $('#cotacao_id').val();
                var fornecedor_id = $('#fornecedor_id').val();
                forma_pagamento = $('#forma_pagamento').val();
                prazo_entrega = $('#prazo_entrega').val();
                tipo_frete = parseInt($('#tipo_frete').val());
                var valor_frete = $('#valor_frete').val().replace('.', '');
                valor_frete = valor_frete.replace(',', '.');
                valor_frete = parseFloat(valor_frete) > 0 ? parseFloat(valor_frete) : 0;
                var faturamento_minimo = $('#faturamento_minimo').val().replace('.', '');
                faturamento_minimo = faturamento_minimo.replace(',', '.');
                faturamento_minimo = parseFloat(faturamento_minimo) > 0 ? parseFloat(faturamento_minimo) : 0;
                var itens = [];

                $('#itensCotacaoTable tbody tr').each(function () {
                    var vu = $(this).find("input[name='valor_unitario[]']").val();
                    if (vu) {
                        vu = vu.replace(',', '.').trim();
                    }
                    else {
                        vu = 0;
                    }
                    var quantidade = parseFloat($(this).find('td').eq(2).text()) || 0;
                    var valor_unitario = parseFloat(vu).toFixed(2) || 0;
                    var cod = parseInt($(this).find("input[name='cod[]']").val() || 0);
                    var obs = $(this).find("input[name='observacao[]']").val();
                    var valor_total = parseFloat(quantidade * valor_unitario).toFixed(2);
                    if (cod > 0) {
                        itens.push({
                            id: cod,
                            quantidade: quantidade,
                            valor_unitario: valor_unitario,
                            valor_total: valor_total,
                            observacao: obs,
                        });
                    }

                });

                //console.log(itens);

                $.ajax({
                    url: '{{ route("fornecedor-cotacao.batch.store.batch") }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        cotacao_id: cotacao_id,
                        fornecedor_id: fornecedor_id,
                        forma_pagamento: forma_pagamento,
                        prazo_entrega: prazo_entrega,
                        tipo_frete: tipo_frete,
                        valor_frete: valor_frete,
                        faturamento_minimo: faturamento_minimo,
                        items: itens,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        console.log(response);
                        $('#loading').hide();
                        alert('Dados enviados com sucesso!');
                        window.location.href = 'https://sistema.abastecejacompras.com.br';
                        // Optionally reload or update UI here
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        $('#loading').hide();
                        alert('Erro ao enviar dados: ' + errorThrown);
                        for (i in XMLHttpRequest) {
                            if (i != "channel")
                                console.log(i + " : " + XMLHttpRequest[i])
                        }
                    }
                });
            });



        });

        function formatCurrency(value) {
            value = value.replace(/\D/g, ""); // Remove tudo que não for dígito
            value = (value / 100).toFixed(2) + ""; // Divide por 100 e fixa 2 casas decimais
            value = value.replace(".", ","); // Substitui ponto por vírgula
            value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1."); // Adiciona os pontos de milhar
            return value;
        }

        function applyMask(input) {
            input.value = formatCurrency(input.value);
        }

        function calculaValorTotal(i, qtd, valor) {
            valor = valor.replace(',', '.');
            var vu = parseFloat(valor).toFixed(2);
            var vrtotal = parseFloat(qtd * vu).toFixed(2);

            $('#vtotal' + i).val(number_format(vrtotal, 2, ',', '.'));
        }

        function number_format(number, decimals, dec_point, thousands_sep) {
            // Strip all characters but numerical ones.
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function (n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }

        function setValorFrete(p) {
            if (p == 1) {
                document.getElementById('dv_val_frete').style.display = '';
            }
            else {
                document.getElementById('dv_val_frete').style.display = 'none';
                $('#valor_frete').val(0);
            }
        }

    </script>

</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <div class="login-logo" style="text-align: center; width: 100%">
                    <a href="https://sistema.abastecejacompras.com.br" style="text-decoration: none; color: black;">
                        <h1>SisCotação</h1>
                    </a>
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto"></ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                <h2>Fornecedor Cotação</h2>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h4>Cotação: #{{ $cotacao->numero }}</h4>
                            <p>Início: {{ \Carbon\Carbon::parse($cotacao->inicio)->format('d/m/Y H:i') }}</p>
                            <p>Encerramento: {{ \Carbon\Carbon::parse($cotacao->encerramento)->format('d/m/Y H:i') }}
                            </p>
                            <p>Status: {{ \App\Helpers\Helper::getStatusCotacao($cotacao->status) }}</p>
                        </div>
                        <div class="mb-3">
                            <h4>Fornecedor: {{ $fornecedor->razao_social }}</h4>
                            <p>Email: {{ $fornecedor->email ?? 'N/A' }}</p>
                            <p>Telefone: {{ $fornecedor->telefone ?? 'N/A' }}</p>
                            <p>Descrição: {{ $cotacao->descricao ?? '' }}</p>
                            <p>Observação: {{ $cotacao->observacao ?? '' }}</p>
                            <p>Endereço de entrega: {{ $cotacao->endereco_entrega }}</p>
                        </div>
                        <div class="mb-3">
                            <label for="forma_pagamento" class="form-label">Forma de Pagamento</label>
                            <select class="form-select" id="forma_pagamento" name="forma_pagamento" required>
                                <option value="1">A vista</option>
                                <option value="2">30 dias</option>
                                <option value="3">60 dias</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tipo_frete" class="form-label">Tipo de Frete</label>
                            <select class="form-select" id="tipo_frete" name="tipo_frete" required
                                onchange=" setValorFrete(this.value)">
                                <option value="">Selecione</option>
                                <option value="1">FOB</option>
                                <option value="2">CIF</option>
                            </select>
                        </div>

                        <div class="mb-3" id="dv_val_frete" style="display: none">
                            <label for="valor_frete" class="form-label">Valor do Frete</label>
                            <input type="text" class="form-control" id="valor_frete" name="valor_frete"
                                placeholder="Informe o valor do frete" style="text-align: right"
                                oninput="applyMask(this)">
                        </div>

                        <div class="mb-3">
                            <label for="faturamento_minimo" class="form-label">Faturamento Mínimo</label>
                            <input type="text" class="form-control" id="faturamento_minimo" name="faturamento_minimo"
                                placeholder="Informe o faturamento mínimo" style="text-align: right"
                                oninput="applyMask(this)">
                        </div>

                        <div class="mb-3">
                            <label for="prazo_entrega" class="form-label">Prazo de Entrega</label>
                            <input type="text" class="form-control" id="prazo_entrega" name="prazo_entrega"
                                placeholder="Informe o prazo de entrega">
                        </div>
                    </div>
                </div>
                <form id="frmCotacaoFornec" method="post">
                    @csrf
                    <div><b>Obs. </b>Ao digitar o valor unitário do produto tecle "Tab", para calcular o Total de cada
                        produto e prosseguir</div>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="itensCotacaoTable">
                                <thead class="table-light">
                                    <tr style="background-color:#C0C0C0">
                                        <th>Produto</th>
                                        <th>Marca</th>
                                        <th>Quantidade</th>
                                        <th>Obs Produto</th>
                                        <th>Unidade</th>
                                        <th>Valor Unitário</th>
                                        <th>Total</th>
                                        <th>Observação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                            $i = 0;
                            ?>
                                    @forelse ($itens_cotacao as $item)
                                        <tr data-id="{{ $item['id'] }}">
                                            <td>{{ $item['produto_nome'] }}</td>
                                            <td>{{ $item['marca_nome'] }}</td>
                                            <td>{{ $item['quantidade'] }}</td>
                                            <td>{{ $item['observacao'] }}</td>
                                            <td>{{ $item['unidade'] }}</td>
                                            <td>
                                                <input type="text" name="valor_unitario[]" id="valor" class="form-control"
                                                    oninput="applyMask(this)"
                                                    onblur="calculaValorTotal({{ $i }}, {{ $item['quantidade'] }}, this.value)"
                                                    style="text-align: right">
                                            </td>
                                            <td>
                                                <input type="text" name="valor_total[]" id="vtotal{{ $i }}"
                                                    class="form-control" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="observacao[]" id="observacao{{ $i }}"
                                                    class="form-control">
                                            </td>
                                            <input type="hidden" name="cod[]" value="{{ $item['id'] }}">
                                        </tr>
                                        <?php 
                                        $i++;
                                        ?>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Nenhum item cadastrado.</td>
                                        </tr>
                                    @endforelse
                                    @if($cotacao->status == 1)
                                        <tr>
                                            <td colspan="8" class="text-end">
                                                <input type="submit" class="btn btn-success" value="Enviar" id="btnEnvio">
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="8">
                                            <button type="button" class="btn btn-success" id="loading"
                                                style="display:none;" disabled>
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span>
                                                Processando...
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="cotacao_id" id="cotacao_id" value="{{ $cotacao["id"] }}">
                        <input type="hidden" name="fornecedor_id" id="fornecedor_id" value="{{ $fornecedor["id"] }}">
                </form>
            </div>
        </main>
    </div>
</body>

</html>