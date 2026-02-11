<div class="modal fade" id="pedidoModal" tabindex="-1" aria-labelledby="pedidoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="pedidoForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="pedidoModalLabel">Novo Pedido</h5>
                    <button type="button" class="btn-close" id="btnClose" data-bs-dismiss="modal"
                        aria-label="Fechar">X</button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="num_pedido" class="form-label">Nº Pedido</label><br>
                        <input type="text" name="num_pedido" id="num_pedido" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="id_fornecedor" class="form-label">Fornecedor</label><br>
                        <select class="form-select" id="id_fornecedor" name="id_fornecedor" required>
                            <option value="">Selecione um fornecedor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="valor" class="form-label">Valor</label><br>
                        <input type="text" step="0.01" class="form-control" id="valor" name="valor" required
                            style="width: 150px; text-align: right" oninput="applyMask(this)" value="0" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="actived" class="form-label">Ativo</label>
                        <select class="form-select" id="actived" name="actived" required>
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="1">Gerado</option>
                            <option value="2">Enviado</option>
                            <option value="3">Cancelado</option>
                            <option value="4">Recebido</option>
                            <option value="5">Aprovado</option>
                        </select>
                    </div>
                    <div id="formErrors" class="text-danger"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btnCancel"
                        data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar Pedido</button>
                </div>
                <button type="button" class="btn btn-success" id="loading" style="display:none; width: 100%" disabled>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Processando...
                </button>

                <input type="hidden" class="form-control" id="id_usuario" name="id_usuario"
                    value="{{ auth()->user()->id }}">
            </form>
        </div>
    </div>
</div>