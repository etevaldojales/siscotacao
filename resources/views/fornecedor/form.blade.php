<div class="modal fade" id="fornecedorModal" tabindex="-1" aria-labelledby="fornecedorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="fornecedorForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="fornecedorModalLabel">Adicionar Fornecedor</h5>
          <button type="button" id="btnCloseForn" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar">X</button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="fornecedorId" name="id" value="0" />
          <div class="row">
            <div class="mb-3 col-md-4">
              <label for="cnpj" class="form-label">CNPJ <span style="color: red;">*</span></label>
              <input type="text" class="form-control" id="cnpj" name="cnpj" required />
            </div>
            <div class="mb-3 col-md-4">
              <label for="razao_social" class="form-label">Razão Social <span style="color: red;">*</span></label>
              <input type="text" class="form-control" id="razao_social" name="razao_social" required />
            </div>
            <div class="mb-3 col-md-4">
              <label for="nome_fantasia" class="form-label">Nome Fantasia</label>
              <input type="text" class="form-control" id="nome_fantasia" name="nome_fantasia" />
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col-md-4">
              <label for="email" class="form-label">Email <span style="color: red;">*</span></label>
              <input type="email" class="form-control" id="email" name="email" required placeholder="E-mail"/>
            </div>
            <div class="mb-3 col-md-4">
              <label for="email2" class="form-label">Email 2</label>
              <input type="email" class="form-control" id="email2" name="email2" />
            </div>
            <div class="mb-3 col-md-4">
              <label for="inscricao_estadual" class="form-label">Inscrição Estadual</label>
              <input type="text" class="form-control" id="inscricao_estadual" name="inscricao_estadual" />
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col-md-3">
              <label for="cep" class="form-label">CEP</label>
              <input type="text" class="form-control" id="cep" name="cep" />
            </div>
            <div class="mb-3 col-md-5">
              <label for="logradouro" class="form-label">Logradouro <span style="color: red;">*</span></label>
              <input type="text" class="form-control" id="logradouro" name="logradouro" required/>
            </div>
            <div class="mb-3 col-md-2">
              <label for="numero" class="form-label">Número <span style="color: red;">*</span></label>
              <input type="text" class="form-control" id="numero" name="numero" required/>
            </div>
            <div class="mb-3 col-md-2">
              <label for="complemento" class="form-label">Complemento</label>
              <input type="text" class="form-control" id="complemento" name="complemento" />
            </div>
            <div class="mb-3 col-md-2">
              <label for="bairro" class="form-label">Bairro</label>
              <input type="text" class="form-control" id="bairro" name="bairro" style="width: 300px"/>
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col-md-4">
              <label for="cidade" class="form-label">Cidade <span style="color: red;">*</span></label>
              <input type="text" class="form-control" id="cidade" name="cidade" required/>
            </div>
            <div class="mb-3 col-md-2">
              <label for="estado" class="form-label">UF <span style="color: red;">*</span></label>
              <input type="text" class="form-control" id="estado" name="estado" maxlength="2" required style="width: 45px;"/>
            </div>
            <div class="mb-3 col-md-2">
              <label for="telefone" class="form-label">Telefone <span style="color: red;">*</span></label>
              <input type="text" class="form-control" id="telefone" name="telefone" required style="width: 130px;"/>
            </div>
            <div class="mb-3 col-md-2" style="margin-left: 20px">
              <label for="celular" class="form-label">Celular <span style="color: red;">*</span></label>
              <input type="text" class="form-control" id="celular" name="celular" required style="width: 130px;"/>
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col-md-3">
              <label for="whatsapp" class="form-label">Whatsapp</label>
              <input type="text" class="form-control" id="whatsapp" name="whatsapp" style="width: 130px;"/>
            </div>            
            <div class="mb-3 col-md-3">
              <label for="tipo" class="form-label">Tipo <span style="color: red;">*</span></label>
              <select class="form-control" id="tipo" name="tipo" required>
                <option value="matriz">Matriz</option>
                <option value="filial">Filial</option>
              </select>
            </div>
            <div class="mb-3 col-md-3">
              <label for="cnpj_matriz" class="form-label">CNPJ Matriz <span style="color: red;">*</span></label>
              <input type="text" class="form-control" id="cnpj_matriz" name="cnpj_matriz" required/>
            </div>
          <div class="row">
          <div class="mb-3 col-md-12">
              <label for="cnpj_comprador" class="form-label">CNPJ Comprador <span style="color: red;">*</span></label>
              <input type="text" class="form-control" id="cnpj_comprador" name="cnpj_comprador" required placeholder="Digite o CNPJ do comprador e tecle Enter" onclick="this.value=''"/>
          </div>

            <div class="mb-3 col-md-12">
              <label for="categorias" class="form-label">Categorias <span style="color: red;">*</span></label>
              @if($categorias->isEmpty())
                <p>Nenhuma categoria disponível</p>
              @else
              <select multiple class="form-control" id="categorias" name="categorias[]" style="height: 150px;" required>
                @foreach($categorias as $categoria)
                  @if (count($fornecedores) > 0 && $fornecedor->categoria_id == $categoria->id)
                  <option value="{{ $categoria->id }}" selected>{{ $categoria->nome }}</option>
                  @else
                  <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                  @endif
                @endforeach
              </select>
              @endif
            </div>
          </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cnpjInput = document.getElementById('cnpj_comprador');
    const categoriasSelect = document.getElementById('categorias');

    cnpjInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
          $('#loadingModal').show();
            $('#btnSaveFornecedor').hide();
            event.preventDefault();
            const cnpj = $('#cnpj_comprador').val().trim();
            if (!cnpj) {
                return;
            }

            $.ajax({
                url: 'categories-by-cnpj',
                type: 'POST',
                data: {
                    cnpj_comprador: cnpj,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(data) {
                    // Clear existing options
                    while (categoriasSelect.firstChild) {
                        categoriasSelect.removeChild(categoriasSelect.firstChild);
                    }

                    if (data.length === 0) {
                      $('#loadingModal').hide();
                        const option = document.createElement('option');
                        option.text = 'Nenhuma categoria disponível';
                        option.disabled = true;
                        categoriasSelect.appendChild(option);
                    } else {
                      $('#loadingModal').hide();
                      $('#btnSaveFornecedor').show();
                        data.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category.id;
                            option.text = category.nome;
                            categoriasSelect.appendChild(option);
                        });
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    for (i in XMLHttpRequest) {
                        if (i != "channel")
                            console.log(i + " : " + XMLHttpRequest[i])
                    }
                }
            });
        }
    });
});
</script>
        </div>
        <div class="modal-footer">
          <button type="button" id="btnCancelForn" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="btnSaveFornecedor">Salvar</button>
        </div>
        <button type="button" class="btn btn-success" id="loadingModal" disabled style="display: none;">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Processando...
        </button>
        <br>
      </div>
    </form>
  </div>
</div>
