$(document).ready(function() {
    var fornecedorModal = new bootstrap.Modal(document.getElementById('fornecedorModal'));

    $('#btnAddFornecedor').click(function() {
        $('#fornecedorForm')[0].reset();
        $('#fornecedorId').val('0');
        $('#fornecedorModalLabel').text('Adicionar Fornecedor');
        fornecedorModal.show();
    });

    $('#fornecedorTable').on('click', '.btnEdit', function() {
        var row = $(this).closest('tr');
        $('#fornecedorId').val(row.data('id'));
        $('#cnpj').val(row.find('td:eq(1)').text());
        $('#razao_social').val(row.find('td:eq(2)').text());
        $('#nome_fantasia').val(row.find('td:eq(3)').text());
        $('#email').val(row.find('td:eq(4)').text());
        $('#email2').val(row.find('td:eq(5)').text());
        $('#inscricao_estadual').val(row.find('td:eq(6)').text());
        $('#cep').val(row.find('td:eq(7)').text());
        $('#logradouro').val(row.find('td:eq(8)').text());
        $('#numero').val(row.find('td:eq(9)').text());
        $('#bairro').val(row.find('td:eq(10)').text());
        $('#cidade').val(row.find('td:eq(11)').text());
        $('#estado').val(row.find('td:eq(12)').text());
        $('#telefone').val(row.find('td:eq(13)').text());
        $('#celular').val(row.find('td:eq(14)').text());
        $('#whatsapp').val(row.find('td:eq(15)').text());
        $('#tipo').val(row.find('td:eq(16)').text());
        $('#cnpj_matriz').val(row.find('td:eq(17)').text());
        $('#fornecedorModalLabel').text('Editar Fornecedor');
        fornecedorModal.show();
    });

    $('#fornecedorTable').on('click', '.btnDelete', function() {
        if (!confirm('Tem certeza que deseja excluir este fornecedor?')) {
            return;
        }
        var row = $(this).closest('tr');
        var id = row.data('id');

        $.ajax({
            url: '/admin/fornecedor/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                row.remove();
            },
            error: function(xhr) {
                alert('Erro ao excluir fornecedor');
            }
        });
    });

    $('#fornecedorForm').submit(function(e) {
        e.preventDefault();

        var id = $('#fornecedorId').val();
        var url = id && id != '0' ? '/admin/fornecedor/' + id : '/admin/fornecedor';
        var type = id && id != '0' ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: type,
            data: {
                cnpj: $('#cnpj').val(),
                razao_social: $('#razao_social').val(),
                nome_fantasia: $('#nome_fantasia').val(),
                email: $('#email').val(),
                email2: $('#email2').val(),
                inscricao_estadual: $('#inscricao_estadual').val(),
                cep: $('#cep').val(),
                logradouro: $('#logradouro').val(),
                numero: $('#numero').val(),
                bairro: $('#bairro').val(),
                cidade: $('#cidade').val(),
                estado: $('#estado').val(),
                telefone: $('#telefone').val(),
                celular: $('#celular').val(),
                whatsapp: $('#whatsapp').val(),
                tipo: $('#tipo').val(),
                cnpj_matriz: $('#cnpj_matriz').val()
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert('Erro ao salvar fornecedor');
            }
        });
    });
});
