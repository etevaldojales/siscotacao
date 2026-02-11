
$(document).ready(function() {
    var categoriaModal = new bootstrap.Modal(document.getElementById('categoriaModal'));

    $('#btnAddCategoria').click(function() {
        $('#categoriaForm')[0].reset();
        $('#categoriaId').val('');
        $('#categoriaModalLabel').text('Adicionar Categoria');
        categoriaModal.show();
    });

    $('#categoriaTable').on('click', '.btnEdit', function() {
        var row = $(this).closest('tr');
        var id = row.data('id');

        $.ajax({
            url: '/admin/categoria/' + id,
            type: 'GET',
            success: function(categoria) {
                $('#categoriaId').val(categoria.id);
                $('#nome').val(categoria.nome);
                $('#descricao').val(categoria.descricao);
                $('#categoriaModalLabel').text('Editar Categoria');

                // Clear previous selections
                $('#users_comprador option').prop('selected', false);

                // Select the usersComprador
                if (categoria.users_comprador) {
                    categoria.users_comprador.forEach(function(user) {
                        $('#users_comprador option[value="' + user.id + '"]').prop('selected', true);
                    });
                }

                categoriaModal.show();
            },
            error: function() {
                alert('Erro ao carregar categoria');
            }
        });
    });

    $('#categoriaTable').on('click', '.btnDelete', function() {
        if (!confirm('Tem certeza que deseja excluir esta categoria?')) {
            return;
        }
        var row = $(this).closest('tr');
        var id = row.data('id');

        $.ajax({
            url: '/admin/categoria/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                row.remove();
            },
            error: function(xhr) {
                alert('Erro ao excluir categoria');
            }
        });
    });

    $('#categoriaForm').submit(function(e) {
        e.preventDefault();

        var id = $('#categoriaId').val();
        var url = id ? '/admin/categoria/' + id : '/admin/categoria';
        var type = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: type,
            data: {
                nome: $('#nome').val(),
                descricao: $('#descricao').val(),
                users_comprador: $('#users_comprador').val()
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert('Erro ao salvar categoria');
            }
        });
    });
});
