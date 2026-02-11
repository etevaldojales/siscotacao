<div class="modal fade" id="itensPedidoModal" tabindex="-1" aria-labelledby="itensPedidoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="itensPedidoForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="itensPedidoModalLabel">Itens do Pedido</h5>
                    <button type="button" class="btn-close" id="btnClose" data-bs-dismiss="modal"
                        aria-label="Fechar">X</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="itemPedidoId" name="id" value="0">
                    <input type="hidden" id="itemPedidoPedidoId" name="pedido_id" value="">

                    <div class="mb-3">
                        <label for="product_id" class="form-label">Produto</label>
                        <select class="form-select" id="product_id" name="product_id" required onchange="carregarMarcasProduto(this.value)">
                            <option value="">Selecione um produto</option>
                            <!-- Options to be loaded dynamically -->
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="marca_id" class="form-label">Marca</label>
                        <select class="form-select" id="marca_id" name="marca_id" required>
                            <option value="">Selecione uma marca</option>
                            <!-- Options to be loaded dynamically -->
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="quantidade" class="form-label">Quantidade</label>
                        <input type="number" class="form-control" id="quantidade" name="quantidade" min="1" required
                            style="width: 150px;">
                    </div>

                    <div class="mb-3">
                        <label for="unidade" class="form-label">Unidade</label>
                        <select class="form-select" id="unidade" name="unidade" required style="width: 150px;">
                            <option value="">Selecione a unidade</option>
                            <option value="1">kg</option>
                            <option value="2">cx</option>
                            <option value="3">unid</option>
                            <option value="4">saco</option>
                            <option value="5">metro</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="valor_unitario" class="form-label">Valor Unitário</label>
                        <input type="text" class="form-control" id="valor_unitario" name="valor_unitario" required
                            style="width: 150px; text-align: right;" oninput="applyMask(this)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="loadingItem" style="display:none;" disabled>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Processando...
                    </button>
                    <button type="button" class="btn btn-secondary" id="btnCancel"
                        data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar Item</button>
                </div>
            </form>
            <h5 style="padding-left: 10px;">Lista ítens</h5>
            <table class="table table-bordered" style="font-size: 13px;">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Marca</th>
                        <th>Qtd</th>
                        <th>Valor Total</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody  id="itensPedidoTable">
                    <!-- Data will be loaded by AJAX -->
                </tbody>
            </table>

        </div>
    </div>
</div>