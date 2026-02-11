@extends('adminlte::page')

@section('content')
    <div class="container mt5">
        <h3 class="modal-title" id="pedidoModalLabel">Novo Pedido</h3>

        <form id="pedidoForm">
            <div style="width: 50%; float: left;">
                <div class="form-group">
                    <label for="num_pedido" class="form-label">Nº Pedido</label><br>
                    <input type="text" name="num_pedido" id="num_pedido" readonly required>
                </div>
                <div class="form-group">
                    <label for="id_fornecedor" class="form-label">Fornecedor</label><br>
                    <select class="form-select" id="id_fornecedor" name="id_fornecedor" required>
                        <option value="">Selecione um fornecedor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="valor" class="form-label">Valor</label><br>
                    <input type="text" step="0.01" class="form-control" id="valor" name="valor" required
                        style="width: 150px; text-align: right" oninput="applyMask(this)" value="0" readonly>
                </div>
                <div class="form-group">
                    <label for="actived" class="form-label">Ativo</label>
                    <select class="form-select" id="actived" name="actived" required>
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="1">Gerado</option>
                        <option value="2">Enviado</option>
                        <option value="3">Cancelado</option>
                        <option value="4">Recebido</option>
                        <option value="5">Aprovado</option>
                    </select>
                </div>
            </div>
            <div style="width: 50%; float: left;">
                <div class="form-group">
                    <label for="forma_pagamento" class="form-label">Forma de Pagamento</label>
                    <select class="form-select" id="forma_pagamento" name="forma_pagamento" required>
                        <option value="1">A vista</option>
                        <option value="2">30 dias</option>
                        <option value="3">60 dias</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tipo_frete" class="form-label">Tipo Frete</label>
                    <select class="form-select" id="tipo_frete" name="tipo_frete" required
                        onchange="setValorFrete(this.value)">
                        <option value="">Selecione o tipo de frete</option>
                        <option value="1">FOB</option>
                        <option value="2">CIF</option>
                    </select>
                </div>
                <div class="mb-3" id="dv_val_frete" style="display: none">
                    <label for="valor_frete" class="form-label">Valor Frete</label>
                    <input type="text" step="0.01" class="form-control" id="valor_frete" name="valor_frete"
                        style="width: 150px; text-align: right" oninput="applyMask(this)">
                </div>
                <div class="mb-3">
                    <label for="observacao" class="form-label">Observação</label>
                    <input type="text" class="form-control" id="observacao" name="observacao" maxlength="255">
                </div>

                <div class="form-group">
                    <label for="prazo_entrega" class="form-label">Prazo de Entrega</label>
                    <input type="text" class="form-control" id="prazo_entrega" name="prazo_entrega"
                        placeholder="Informe o prazo de entrega" style="width: 300px;">
                </div>
            </div>

            <div id="formErrors" class="text-danger" style="clear: both"></div>

            <button type="submit" id="btnsalva" class="btn btn-primary">Salvar</button>
            <button type="reset" class="btn btn-warning">Cancelar</button>
            <button type="button" class="btn btn-success" id="loading" style="display:none;" disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Processando...
            </button>

            <input type="hidden" class="form-control" id="id_usuario" name="id_usuario" value="{{ auth()->user()->id }}">
        </form><br>

        <h3>Pedidos</h3>

        <div class="mb-3">
            <input type="text" class="form-control" id="searchPedido" placeholder="Buscar pedido..." onclick="this.value=''"
                style="width: 350px; float: left">
            <input type="button" class="btn btn-primary" value="Buscar Todos" onclick="pesquisarPedidos()"
                style="clear: both; margin-left: 8px;">
            <button type="button" class="btn btn-success" id="loadingSearch" style="display:none;" disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Processando...
            </button>
        </div>

        <table class="table table-bordered" id="pedidos-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuário</th>
                    <th>Fornecedor</th>
                    <th>Número</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be loaded by AJAX -->
            </tbody>
        </table>
    </div>

    <script>
        // Add edit button to each row dynamically after loading pedidos
        function addEditButtons() {
            $('#pedidos-table tbody tr').each(function () {
                var $actionsCell = $(this).find('td').last();
                if ($actionsCell.find('.editPedidoBtn').length === 0) {
                    var pedidoId = $(this).find('td:first').text().trim();
                    var editBtn = $('<button>')
                        .addClass('btn btn-info btn-sm editPedidoBtn')
                        .text('Editar')
                        .attr('data-id', pedidoId)
                        .css('margin-left', '8px');
                    $actionsCell.append(editBtn);
                }
            });
        }
    </script>

    <!-- Modal -->
    @include('pedidos.item_form')
    </div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="{{ asset('js/pedido.js') }}"></script>
<script src="{{ asset('js/itens_pedido.js') }}"></script>
@stop