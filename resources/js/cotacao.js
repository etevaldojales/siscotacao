$(document).ready(function() {
    // CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Function to fetch data with AJAX
    function fetchData(page = 1, query = '') {
        $('#loading').show();
        $.ajax({
            url: '/cotacao',
            type: 'GET',
            data: { page: page, query: query },
            dataType: 'json',
            success: function(data) {
                $('#table-container').html(data.table);
                $('#pagination-container').html(data.pagination);
                $('#loading').hide();
            },
            error: function() {
                alert('Erro ao carregar dados.');
                $('#loading').hide();
            }
        });
    }

    // Initial fetch
    fetchData();

    // Handle pagination link click
    $(document).on('click', '#pagination-container a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        var query = $('#searchCotacao').val();
        fetchData(page, query);
    });

    // Handle search button click
    $('#searchCotacao').on('keypress', function(e) {
        if (e.which == 13) { // Enter key pressed
            var query = $(this).val();
            fetchData(1, query);
        }
    });

    $('input[value="Buscar Todos"]').on('click', function() {
        $('#searchCotacao').val('');
        fetchData(1, '');
    });

    // New: Handle btnAddItens click to load products and categories for logged-in comprador user
    $(document).on('click', '.btnAddItens', function() {
        $.ajax({
            url: '/admin/produtos-categorias-comprador',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                // Assuming you have a modal or section to show products and categories
                // For example, populate #productsList and #categoriesList elements
                var produtosHtml = '';
                $.each(response.produtos, function(index, produto) {
                    produtosHtml += '<li>' + produto.name + '</li>';
                });
                $('#productsList').html(produtosHtml);

                var categoriasHtml = '';
                $.each(response.categorias, function(index, categoria) {
                    categoriasHtml += '<li>' + categoria.nome + '</li>';
                });
                $('#categoriesList').html(categoriasHtml);

                // Show the modal or section if needed
                $('#produtosCategoriasModal').modal('show');
            },
            error: function(xhr) {
                alert('Erro ao carregar produtos e categorias: ' + (xhr.responseJSON ? xhr.responseJSON.error : 'Erro desconhecido'));
            }
        });
    });
});
