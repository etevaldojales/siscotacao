$(document).ready(function () {
    //$('#loading').show();
    var fornecedorModal = new bootstrap.Modal(
        document.getElementById("fornecedorModal")
    );

    $("#btnAddFornecedor").click(function () {
        $("#fornecedorForm")[0].reset();
        $("#fornecedorId").val("0");
        $("#fornecedorModalLabel").text("Adicionar Fornecedor");
        fornecedorModal.show();
    });

    $("#fornecedorTable").on("click", ".btnEdit", function () {
        $('#loading').show();
        var row = $(this).closest("tr");
        $("#fornecedorId").val(row.data("id"));
        //console.log("ID: " + row.data("id"));
        $.ajax({
            type: "POST",
            url: "carrega-fornecedor",
            data: { id: row.data("id") },
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                $('#loading').hide();
                $("#cnpj").val(data.cnpj);
                $("#razao_social").val(data.razao_social);
                $("#nome_fantasia").val(data.nome_fantasia);
                $("#email").val(data.email);
                $("#email2").val(data.email2);
                $("#inscricao_estadual").val(data.inscricao_estadual);
                $("#cep").val(data.cep);
                $("#logradouro").val(data.logradouro);
                $("#numero").val(data.numero);
                $("#complemento").val(data.complemento);
                $("#bairro").val(data.bairro);
                $("#cidade").val(data.cidade);
                $("#estado").val(data.estado);
                $("#telefone").val(data.telefone);
                $("#celular").val(data.celular);
                $("#whatsapp").val(data.whatsapp);
                $("#tipo").val(data.tipo);
            $("#cnpj_matriz").val(data.cnpj_matriz);

            // Set selected categories
            if (data.categorias) {
                $("#categorias").val(data.categorias.map(function(cat) { return cat.id.toString(); }));
            } else {
                $("#categorias").val([]);
            }

            $("#fornecedorModalLabel").text("Editar Fornecedor");
            fornecedorModal.show();
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            for (i in XMLHttpRequest) {
                if (i != "channel")
                    console.log(i + " : " + XMLHttpRequest[i]);
            }
        },
    });
});

    $("#fornecedorTable").on("click", ".btnDelete", function () {
        if (!confirm("Tem certeza que deseja excluir este fornecedor?")) {
            return;
        }
        var row = $(this).closest("tr");
        var id = row.data("id");

        $.ajax({
            url: "remover-fornecedor",
            type: "POST",
            data: { id: id },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                row.remove();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        });
    });

    $("#fornecedorForm").submit(function (e) {
        e.preventDefault();
        $("#loadingModal").show();
        
        var id = $("#fornecedorId").val();
        var url = "salvar-fornecedor";
        var type = "POST";

        $.ajax({
            url: url,
            type: type,
            dataType: "json",
            data: {
                id: id,
                cnpj: $("#cnpj").val(),
                razao_social: $("#razao_social").val(),
                nome_fantasia: $("#nome_fantasia").val(),
                email: $("#email").val(),
                email2: $("#email2").val(),
                inscricao_estadual: $("#inscricao_estadual").val(),
                cep: $("#cep").val(),
                logradouro: $("#logradouro").val(),
                numero: $("#numero").val(),
                complemento: $("#complemento").val(),
                bairro: $("#bairro").val(),
                cidade: $("#cidade").val(),
                estado: $("#estado").val(),
                telefone: $("#telefone").val(),
                celular: $("#celular").val(),
                whatsapp: $("#whatsapp").val(),
                tipo: $("#tipo").val(),
            cnpj_matriz: $("#cnpj_matriz").val(),
                categorias: $("#categorias").val() || [],
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            $("#loading").hide();
            location.reload();
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            for (i in XMLHttpRequest) {
                if (i != "channel")
                    console.log(i + " : " + XMLHttpRequest[i]);
            }
        },
    });
});

    $("#btnCloseForn").on("click", function () {
        fornecedorModal.hide();
    });

    $("#btnCancelForn").on("click", function () {
        fornecedorModal.hide();
    });

    // Ajax search for fornecedores
    $("#searchFornecedor").on("input", function () {
        var query = $(this).val();
        $("#loading").show();

        $.ajax({
            url: "fornecedores-search",
            type: "post",
            data: { query: query },
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                $("#loading").hide();
                var tbody = $("#fornecedorTable tbody");
                tbody.empty();

                if (data.length === 0) {
                    tbody.append(
                        '<tr><td colspan="7" class="text-center">Nenhum fornecedor encontrado.</td></tr>'
                    );
                } else {
                    $.each(data, function (index, fornecedor) {
                        var rowColor = index % 2 === 0 ? "#FOF8FF" : "#DCDCDC";
                        var row =
                            '<tr data-id="' +
                            fornecedor.id +
                            '" style="background-color: ' +
                            rowColor +
                            '">' +
                            "<td>" +
                            fornecedor.cnpj +
                            "</td>" +
                            "<td>" +
                            fornecedor.razao_social +
                            "</td>" +
                            "<td>" +
                            fornecedor.email +
                            "</td>" +
                            "<td>" +
                            fornecedor.telefone +
                            "</td>" +
                            "<td>" +
                            fornecedor.celular +
                            "</td>" +
                            "<td>" +
                            fornecedor.tipo +
                            "</td>" +
                            "<td>" +
                            '<nobr><button class="btn btn-sm btn-warning btnEdit">Editar</button> ' +
                            '<button class="btn btn-sm btn-danger btnDelete">Excluir</button></nobr>' +
                            "</td>" +
                            "</tr>";
                        tbody.append(row);
                    });
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        });
    });
});

async function pesquisarFornecedores() {
    $("#searchFornecedor").val("");
    $("#loading").show();
    $.ajax({
        url: "fornecedores-search",
        type: "POST",
        data: { query: "" },
        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"), },
        dataType: "json",
        success: function (data) {
            $("#loading").hide();
            var tbody = $("#fornecedorTable tbody");
            tbody.empty();

            if (data.length === 0) {
                tbody.append(
                    '<tr><td colspan="7" class="text-center">Nenhum fornecedor encontrado.</td></tr>'
                );
            } else {
                $.each(data, function (index, fornecedor) {
                    var rowColor = index % 2 === 0 ? "#FOF8FF" : "#DCDCDC";
                    var row =
                        '<tr data-id="' +
                        fornecedor.id +
                        '" style="background-color: ' +
                        rowColor +
                        '">' +
                        "<td>" +
                        fornecedor.cnpj +
                        "</td>" +
                        "<td>" +
                        fornecedor.razao_social +
                        "</td>" +
                        "<td>" +
                        fornecedor.email +
                        "</td>" +
                        "<td>" +
                        fornecedor.telefone +
                        "</td>" +
                        "<td>" +
                        fornecedor.celular +
                        "</td>" +
                        "<td>" +
                        fornecedor.tipo +
                        "</td>" +
                        "<td>" +
                        '<nobr><button class="btn btn-sm btn-warning btnEdit">Editar</button> ' +
                        '<button class="btn btn-sm btn-danger btnDelete">Excluir</button></nobr>' +
                        "</td>" +
                        "</tr>";
                    tbody.append(row);
                });
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            for (i in XMLHttpRequest) {
                if (i != "channel") console.log(i + " : " + XMLHttpRequest[i]);
            }
        },
    });
}

    $("#cep").on("blur", function () {
        var cep = $(this).val().replace(/\D/g, "");
        if (cep.length === 8) {
            $.ajax({
                url: "https://viacep.com.br/ws/" + cep + "/json/",
                dataType: "json",
                success: function (data) {
                    if (!("erro" in data)) {
                        $("#logradouro").val(data.logradouro);
                        $("#bairro").val(data.bairro);
                        $("#cidade").val(data.localidade);
                        $("#estado").val(data.uf);
                        $("#numero").focus();
                    } else {
                        alert("CEP não encontrado.");
                        $("#logradouro").val("");
                        $("#bairro").val("");
                        $("#cidade").val("");
                        $("#estado").val("");
                    }
                },
                error: function () {
                    alert("Erro ao consultar o CEP.");
                },
            });
        } else {
            alert("Formato de CEP inválido.");
            $("#logradouro").val("");
            $("#bairro").val("");
            $("#cidade").val("");
            $("#estado").val("");
        }
    });

    // Add CNPJ mask to inputs
    $("#cnpj").mask("00.000.000/0000-00");
    $("#cnpj_matriz").mask("00.000.000/0000-00");
