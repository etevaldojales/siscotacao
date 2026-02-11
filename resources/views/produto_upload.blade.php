<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Upload Excel - Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2>Upload Excel para Produtos</h2>
        <form id="uploadForm" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="excel_file" class="form-label">Selecione o arquivo Excel</label>
                <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xls,.xlsx" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
            <button type="button" class="btn btn-success" id="loading" style="display:none;" disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Processando...
            </button>
        </form>
        <div id="responseMessage" class="mt-3"></div>
    </div>

    <script>

        $(document).ready(function () {
            $('#uploadForm').on('submit', function (e) {
                e.preventDefault();
                $('#loading').show();

                var formData = new FormData(this);
                //console.log(formData);

                $.ajax({
                    url: '{{ route("produto.upload") }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    cache: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        console.log(response);
                        $('#loading').hide();
                        $('#responseMessage').html('<div class="alert alert-success">' + response.success + '</div>');
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        var errorMsg = 'Erro ao enviar arquivo.';
                        for (i in XMLHttpRequest) {
                            if (i != "channel")
                                console.log(i + " : " + XMLHttpRequest[i])
                        }
                        $('#loading').hide();
                        $('#responseMessage').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                    }
                });
            });
        });
    </script>
</body>

</html>