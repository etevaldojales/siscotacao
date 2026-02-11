@extends('adminlte::page')



@section('content')

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <div class="container">

    <h1>Perfis de Usuários</h1>



    @if(session('success'))

    <div class="alert alert-success">{{ session('success') }}</div>

    @endif



    <table class="table table-bordered" id="usersTable">

    <thead class="thead-light">

      <tr>

      <td colspan="4">

        <button type="button" class="btn btn-success" id="loading" style="display:none;" disabled>

        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>

        Processando...

        </button>

      </td>

      </tr>

      <tr>

      <th>Nome</th>

      <th>Email</th>

      <th>Empresa</th>

      <th>Descrição</th>

      <th>CNPJ</th>

      <th>Funções</th>

      <th>Ações</th>

      </tr>

    </thead>

    <tbody>

      @foreach ($users as $user)

      <tr>

      <form method="POST" action="{{ route('users.updateRoles', $user) }}">

      @csrf

      @method('POST')

      <td>{{ $user->name }}</td>

      <td>{{ $user->email }}</td>

      <td>{{ $user->empresa }}</td>

      <td>{{ $user->descricao }}</td>

      <td>{{ $user->cnpj }}</td>

      <td>

      @foreach ($roles as $role)

      <div class="form-check form-check-inline">

      <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}"
      id="role_{{ $user->id }}_{{ $role->id }}" {{ $user->roles->contains($role) ? 'checked' : '' }}>

      <label class="form-check-label" for="role_{{ $user->id }}_{{ $role->id }}">{{ $role->name }}</label>

      </div>

      @endforeach

      </td>

      <td>

      <nobr>

        <button type="submit" class="btn btn-primary btn-sm">Atualizar</button>

        <button type="button" class="btn btn-success btn-sm" id="btnEdit">Editar</button>

      </nobr>

      </td>

      </form>

      </tr>

    @endforeach

    </tbody>

    </table>

  </div>



  <!-- User Edit Modal -->

  <div class="modal fade" id="userEditModal" tabindex="-1" aria-labelledby="userEditModalLabel" aria-hidden="true">

    <div class="modal-dialog">

    <form method="POST" id="userEditForm">

      @csrf

      <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title" id="userEditModalLabel">Editar Usuário</h5>

        <button type="button" class="btn-close" id="btnClose" data-bs-dismiss="modal" aria-label="Fechar">X</button>

      </div>

      <div class="modal-body">

        <input type="hidden" name="user_id" id="editUserId" />

        <div class="mb-3">

        <label for="editName" class="form-label">Nome</label>

        <input type="text" class="form-control" id="editName" name="name" placeholder="Nome do usuário" />

        </div>

        <div class="mb-3">

        <label for="editDescricao" class="form-label">Descrição</label>

        <input type="text" class="form-control" id="editDescricao" name="descricao" placeholder="Ex: LOJA 1" />

        </div>

        <div class="mb-3">

        <label for="editEmpresa" class="form-label">Empresa</label>

        <input type="text" class="form-control" id="editEmpresa" name="empresa" />

        </div>

        <div class="mb-3">
        <label for="editCnpj" class="form-label">CNPJ</label>
        <input type="text" class="form-control" id="editCnpj" name="cnpj" required />
        </div>

        <div class="mb-3">
        <label for="editPassword" class="form-label">Nova Senha (opcional)</label>
        <input type="password" class="form-control" id="editPassword" name="password" placeholder="Deixe em branco para manter a senha atual" />
        </div>

        <div class="mb-3">
        <label for="editPasswordConfirmation" class="form-label">Confirmar Nova Senha</label>
        <input type="password" class="form-control" id="editPasswordConfirmation" name="password_confirmation" placeholder="Confirme a nova senha" />
        </div>

      </div>

      <div class="modal-footer">

        <button type="button" class="btn btn-secondary" id="btnCancel" data-bs-dismiss="modal">Fechar</button>

        <button type="submit" class="btn btn-primary" id="btnSalva">Salvar Alterações</button>

      </div>

      </div>

    </form>

    </div>

  </div>

@endsection

@section('js')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>



  document.addEventListener('DOMContentLoaded', function () {



    $('#editCnpj').mask('00.000.000/0000-00', { reverse: true });

    //const userEditModal = new bootstrap.Modal(document.getElementById('userEditModal'));

    const userEditModal = new bootstrap.Modal(document.getElementById('userEditModal'));

    const userEditForm = document.getElementById('userEditForm');



    // Attach click event to all btnEdit buttons

    document.querySelectorAll('button[id="btnEdit"]').forEach(button => {

      button.addEventListener('click', function () {

        $('#loading').show();

        const tr = this.closest('tr');



        const userId = tr.querySelector('form').action.match(/users\/roles\/(\d+)/)[1];

        /*

        const name = tr.querySelector('td:nth-child(1)').innerText.trim();

        const email = tr.querySelector('td:nth-child(2)').innerText.trim();*/



        //console.log(userId);



        // For descricao, empresa, cnpj, we need to fetch or store them somewhere

        // For now, we will fetch via AJAX or set empty (to be improved)

        // Let's assume we fetch via AJAX here



        $.ajax({

          url: 'user-get',

          method: 'POST',

          dataType: 'json',

          data: { id: userId },

          headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

          },

          success: function (data) {

            $('#loading').hide();

            document.getElementById('editUserId').value = userId;

            document.getElementById('editName').value = data.name || '';

            document.getElementById('editDescricao').value = data.descricao || '';

            document.getElementById('editEmpresa').value = data.empresa || '';

            document.getElementById('editCnpj').value = data.cnpj || '';

            userEditModal.show();

          },

          error: function (XMLHttpRequest, textStatus, errorThrown) {

            $('#loading').hide();

            for (i in XMLHttpRequest) {

              if (i != "channel")

                console.log(i + " : " + XMLHttpRequest[i])

            }

            alert('Falha ao carregar dados');

            userEditModal.show();

          }

        });

      });



      $('#btnClose').on('click', function () {
        userEditModal.hide();
        document.getElementById('editPassword').value = '';
        document.getElementById('editPasswordConfirmation').value = '';
      });



      $('#btnCancel').on('click', function () {
        userEditModal.hide();
        document.getElementById('editPassword').value = '';
        document.getElementById('editPasswordConfirmation').value = '';
      });

    });





    userEditForm.addEventListener('submit', function (e) {

      $('#loading').show();

      e.preventDefault();



      const userId = document.getElementById('editUserId').value;

      const formData = new FormData(userEditForm);



      $.ajax({

        url: 'user-save',

        method: 'POST',

        headers: {

          'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,

          'Accept': 'application/json',

        },

        data: formData,

        processData: false,

        contentType: false,

        success: function (response) {

          console.log(response);

          alert(response.message);

          $('#loading').hide();

          location.reload();

        },

        error: function (XMLHttpRequest, textStatus, errorThrown) {

          $('#loading').hide();

          for (i in XMLHttpRequest) {

            if (i != "channel")

              console.log(i + " : " + XMLHttpRequest[i])

          }

          alert('Erro ao salvar alterações.');

          userEditModal.show();

        }



      });

    });

  });

</script>

@stop