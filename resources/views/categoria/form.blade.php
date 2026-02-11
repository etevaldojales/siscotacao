<div class="modal fade" id="categoriaModal" tabindex="-1" aria-labelledby="categoriaModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <form id="categoriaForm">

      @csrf

      <div class="modal-content">

        <div class="modal-header">

          <h5 class="modal-title" id="categoriaModalLabel">Adicionar Categoria</h5>

          <button type="button" id="btnClose" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">X</button>

        </div>

        <div class="modal-body">

          <input type="hidden" id="categoriaId" name="id" value="0" />

          <div class="mb-3">

            <label for="nome" class="form-label">Nome</label>

            <input type="text" class="form-control" id="nome" name="nome" required />

          </div>

          <div class="mb-3">

            <label for="descricao" class="form-label">Descrição</label>

            <textarea class="form-control" id="descricao" name="descricao"></textarea>

          </div>

          <div class="mb-3">

            <label for="cnpj_comprador" class="form-label">CNPJ Comprador</label>
            <input type="text" class="form-control" id="cnpj_comprador" name="cnpj_comprador" required />
          </div>

        </div>

        <button type="button" class="btn btn-success" id="loading" style="display:none;" disabled>

            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>

            Processando...

        </button>        

        <div class="modal-footer">

          <button type="button" id="btnCancel" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

          <button type="submit" class="btn btn-primary" id="btnSaveCategoria">Salvar</button>

        </div>

      </div>

    </form>

  </div>

</div>

