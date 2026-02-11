//const { method } = require("lodash");



//const { method } = require("lodash");



    $(document).ready(function () {

        var categoriaModal = new bootstrap.Modal(document.getElementById('categoriaModal'));



        $('#btnAddCategoria').click(function () {

            $('#categoriaForm')[0].reset();

            $('#categoriaId').val(0);

            $('#categoriaModalLabel').text('Adicionar Categoria');

            categoriaModal.show();

        });



        $('#categoriaTable').on('click', '.btnEdit', function () {

            var row = $(this).closest('tr');

            var id = row.data('id');

            var nome = row.find('td:eq(1)').text();

            var descricao = row.find('td:eq(2)').text();

            var cnpjComprador = row.find('td:eq(3)').text();



            $('#categoriaId').val(id);

            $('#nome').val(nome);

            $('#descricao').val(descricao);

            $('#cnpj_comprador').val(cnpjComprador);

            $('#categoriaModalLabel').text('Editar Categoria');

            categoriaModal.show();

        });



        $('#categoriaTable').on('click', '.btnDelete', function () {

            if (!confirm('Tem certeza que deseja excluir esta categoria?')) {

                return;

            }

            $('#loading').show();

            var row = $(this).closest('tr');

            var id = row.data('id');



            $.ajax({

                url: 'remover-categoria',

                type: 'POST',

                data: { id: id },

                headers: {

                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                },

                success: function (response) {

                    $('#loading').hide();

                    row.remove();

                },

                error: function (XMLHttpRequest, textStatus, errorThrown) {

                    for (i in XMLHttpRequest) {

                        if (i != "channel")

                            console.log(i + " : " + XMLHttpRequest[i])

                    }

                }



            });

        });



        $('#categoriaForm').submit(function (e) {

            e.preventDefault();

            $('#loading').show();

            var id = $('#categoriaId').val();

            //var url = {{ route('') }};

            //var type = id ? 'POST' : 'POST';



            $.ajax({

                url: 'salvar-categoria',

                method: 'post',

                dataType: 'json',

                cache: false,

                data: {

                    nome: $('#nome').val(),

                    descricao: $('#descricao').val(),

                    cnpj_comprador: $('#cnpj_comprador').val(),

                    id: id

                },

                headers: {

                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                },

                success: function (response) {

                    //console.log('Retorno: ' + response);

                    $('#loading').hide();

                    location.reload();

                },

                error: function (XMLHttpRequest, textStatus, errorThrown) {

                    for (i in XMLHttpRequest) {

                        if (i != "channel")

                            console.log(i + " : " + XMLHttpRequest[i])

                    }

                }

            });

        });



        $('#btnClose').on('click', function () {

            categoriaModal.hide();

        });



        $('#btnCancel').on('click', function () {

            categoriaModal.hide();

        });

    });

