<div class="modal fade" id="itensCotacaoModal" tabindex="-1" aria-labelledby="itensCotacaoModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="itensCotacaoForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="itensCotacaoModalLabel">Adicionar Ítem Cotação</h5>
          <button type="button" id="btnCloseItensCot" class="btn-close" data-bs-dismiss="modal"
            aria-label="Fechar">X</button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="itemCotacaoId" name="id" value="0" />
          <input type="hidden" id="itemCotacaoCotacaoId" name="cotacao_id" value="0" />
          <div class="row mb-3">
            <label for="filtroProduto" class="col-sm-2 col-form-label">Pesquisar</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="filtroProduto" name="filtroProduto"
                placeholder="Digite o nome do produto para pesquisar" />
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col-md-6">
              <label for="product_id" class="form-label">Produto</label>
              <select class="form-control" id="product_id" name="product_id" required>
                <option value="" disabled selected>Selecione</option>
                @foreach($produtos as $produto)
                  <option value="{{ $produto->id }}">{{ $produto->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3 col-md-6">
              <label for="marca_id" class="form-label">Marca</label>
              <select class="form-control select2" id="marca_id" name="marca_id" required>
                <option value="" disabled selected>Selecione</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col-md-4">
              <label for="quantidade" class="form-label">Quantidade</label>
              <input type="number" class="form-control" id="quantidade" name="quantidade" required />
            </div>
            <div class="mb-3 col-md-4">
              <label for="unidade" class="form-label">Unidade</label>
              <select class="form-control" id="unidade" name="unidade" required>
                <option value="">Selecione a unidade</option>
                <option value="1">kg</option>
                <option value="2">cx</option>
                <option value="3">unid</option>
                <option value="4">saco</option>
                <option value="5">metro</option>
              </select>
            </div>
            <div class="mb-3 col-md-4">
              <label for="observacao" class="form-label">Observação</label>
              <textarea class="form-control" id="observacao_item" name="observacao_item" rows="1"></textarea>
            </div>
          </div>
          <div class="row" id="rvalor" style="display: none">
            <div class="mb-3 col-md-6">
              <label for="valor" class="form-label">Valor</label>
              <input type="number" step="0.01" class="form-control" id="valor" name="valor" readonly />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" id="btnSaveItensCot">Salvar</button>
        </div>
        <button type="button" class="btn btn-success" id="loadingItensCotModal" disabled style="display: none;">
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          Processando...
        </button>
        <br>
        <div class="row" id="itensCotacaoTable"></div>
        <br>
        <div class="modal-footer">
          <button type="button" id="btnCancelItensCot" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>

    </form>
  </div>
</div>

<!-- Include Chosen CSS and JS -->


<script>
  document.addEventListener('DOMContentLoaded', function () {


    const path = ''; //public';
    //console.log('Current path:', path);
    const productSelect = document.getElementById('product_id');
    const marcaSelect = document.getElementById('marca_id');
    const filtroProdutoInput = document.getElementById('filtroProduto');

    filtroProdutoInput.addEventListener('keydown', function (event) {
      if (event.key === 'Enter' || event.keyCode === 13) {
        event.preventDefault();
        const searchTerm = this.value;
        $('#product_id').append(new Option('Pesquisando...', '', true, true));
        $.ajax({
          type: 'GET',
          url: path + '/admin/produtos-filter',
          data: { search: searchTerm },
          success: function (response) {
            //console.log('Produtos filtrados:', response);
            // Clear current options
            $('#product_id').empty();

            if (response.length === 0) {
              $('#product_id').append(new Option('Nenhum produto encontrado', '', false, false));
            } else {
              $('#product_id').append(new Option('Selecione', '', true, true));
              $.each(response, function (index, produto) {
                var option = new Option(produto.name, produto.id, false, false);
                $('#product_id').append(option);
              });
            }

            // Update Chosen plugin
            $('#product_id').trigger('chosen:updated');
          },
          error: function (XMLHttpRequest, textStatus, errorThrown) {
            for (i in XMLHttpRequest) {
              if (i != "channel")
                console.log(i + " : " + XMLHttpRequest[i]);
            }
          },
        });
      }
    });

    productSelect.addEventListener('change', function () {
      const produtoId = this.value;

      // Clear marca options and reset Select2
      $('#marca_id').empty().trigger('change');
      $('#marca_id').append(new Option('Carregando...', '', false, false)).trigger('change');

      if (produtoId) {
        $.ajax({
          type: 'POST',
          url: 'marcas-produto',
          data: { id_produto: produtoId },
          headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
          },
            success: function (response) {
              $('#marca_id').empty().trigger('change');
              $('#marca_id').append(new Option('Selecione', '', false, false)).trigger('change');
              $.each(response, function (index, item) {
                var option = new Option(item.nome, item.id, false, false);
                $('#marca_id').append(option);
              });
              if (response.length === 1) {
                $('#marca_id option:eq(1)').prop('selected', true);
              }
              $('#marca_id').trigger('change');
            },
          error: function (XMLHttpRequest, textStatus, errorThrown) {
            for (i in XMLHttpRequest) {
              if (i != "channel")
                console.log(i + " : " + XMLHttpRequest[i]);
            }
          },
        });
      }
    });
  });
</script>
