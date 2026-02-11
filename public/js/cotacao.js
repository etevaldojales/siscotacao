$(document).ready(function () {
    $("#valor").mask("000.000.000.000.000,00", { reverse: true });

    var cotacaoModal = new bootstrap.Modal(
        document.getElementById("cotacaoModal")
    );

    var itensCotacaoModal = new bootstrap.Modal(
        document.getElementById("itensCotacaoModal")
    );

    $("#btnAddCotacao").click(function () {
        $("#cotacaoForm")[0].reset();
        $("#cotacaoId").val("0");

        // Generate unique quotation number
        function pad(num) {
            return num.toString().padStart(2, "0");
        }
        var now = new Date();
        var year = now.getFullYear();
        var month = pad(now.getMonth() + 1);
        var day = pad(now.getDate());
        var hour = pad(now.getHours());
        var minute = pad(now.getMinutes());
        var second = pad(now.getSeconds());
        var userId = window.loggedInUserId || "0";

        var uniqueNumber =
            "" + year + month + day + hour + minute + second + userId;
        $("#numero").val(uniqueNumber);

        $("#cotacaoModalLabel").text("Adicionar Cotação");
        cotacaoModal.show();
    });

    $("#cotacaoTable").on("click", ".btnEdit", function () {
        $("#loading").show();
        var row = $(this).closest("tr");
        $("#cotacaoId").val(row.data("id"));
        $.ajax({
            type: "POST",
            url: "carrega-cotacao",
            data: { id: row.data("id") },
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                var dti = new Date(data.inicio);
                var dtf = new Date(data.encerramento);
                $("#loading").hide();
                $("#numero").val(data.numero);
                $("#inicio").val(
                    data.inicio ? dti.toISOString().slice(0, 16) : ""
                );
                $("#encerramento").val(
                    data.encerramento ? dtf.toISOString().slice(0, 16) : ""
                );
                $("#status").val(data.status);
                $("#status_envio").val(data.status_envio);
                $("#descricao").val(data.descricao);
                $("#observacao").val(data.observacao);
                $("#endereco_entrega").val(data.endereco_entrega);
                $("#cotacaoModalLabel").text("Editar Cotação");
                cotacaoModal.show();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        });
    });

    $("#cotacaoTable").on("click", ".btnDelete", function () {
        if (!confirm("Tem certeza que deseja excluir esta cotação?")) {
            return;
        }
        var row = $(this).closest("tr");
        var id = row.data("id");

        $.ajax({
            url: "remover-cotacao",
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

    $("#cotacaoForm").submit(function (e) {
        e.preventDefault();
        $("#loadingModal").show();

        var id = $("#cotacaoId").val();
        var url = "salvar-cotacao";
        var type = "POST";

        $.ajax({
            url: url,
            type: type,
            dataType: "json",
            data: {
                id: id,
                numero: $("#numero").val(),
                inicio: $("#inicio").val(),
                encerramento: $("#encerramento").val(),
                status: $("#status").val(),
                status_envio: $("#status_envio").val(),
                observacao: $("#observacao").val(),
                descricao: $("#descricao").val(),
                endereco_entrega: $("#endereco_entrega").val(),
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                $("#loading").hide();
                //console.log(response);
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

    // Ajax search for cotacoes
    $("#searchCotacao").on("input", function () {
        var query = $(this).val();
        $("#loading").show();

        $.ajax({
            url: "cotacoes-search",
            type: "post",
            data: { query: query },
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                $("#loading").hide();
                var tbody = $("#cotacaoTable tbody");
                tbody.empty();

                if (data.length === 0) {
                    tbody.append(
                        '<tr><td colspan="5" class="text-center">Nenhuma cotação encontrada.</td></tr>'
                    );
                } else {
                    $.each(data, function (index, cotacao) {
                        var rowColor = index % 2 === 0 ? "#FOF8FF" : "#DCDCDC";
                        var row =
                            '<tr data-id="' +
                            cotacao.id +
                            '" style="background-color: ' +
                            rowColor +
                            '">' +
                            "<td>" +
                            cotacao.numero +
                            "</td>" +
                            "<td>" +
                            cotacao.encerramento +
                            "</td>" +
                            "<td>" +
                            cotacao.status +
                            "</td>" +
                            "<td>" +
                            cotacao.status_envio +
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

    $("#btnCloseCot").on("click", function () {
        cotacaoModal.hide();
    });

    $("#btnCancelCot").on("click", function () {
        cotacaoModal.hide();
    });

    // Itens Cotacao handlers

    $("#cotacaoTable").on("click", ".btnAddItens", function () {
        var row = $(this).closest("tr");
        var cotacaoId = row.data("id");
        $("#itemCotacaoCotacaoId").val(cotacaoId);
        $("#itensCotacaoForm")[0].reset();
        $("#itemCotacaoId").val("0");
        $("#itensCotacaoModalLabel").text("Adicionar Ítem Cotação");
        itensCotacaoModal.show();
        loadItensCotacao(cotacaoId);
    });

    $("#itensCotacaoForm").submit(function (e) {
        e.preventDefault();
        $("#loadingItensCotModal").show();

        var formData = {
            id: $("#itemCotacaoId").val(),
            cotacao_id: $("#itemCotacaoCotacaoId").val(),
            product_id: $("#product_id").val(),
            marca_id: $("#marca_id").val(),
            quantidade: $("#quantidade").val(),
            unidade: $("#unidade").val(),
            observacao: $("#observacao_item").val(),
            valor: 0,
        };

        $.ajax({
            url: "itens-cotacao-store",
            type: "POST",
            dataType: "json",
            data: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                $("#loadingItensCotModal").hide();
                //itensCotacaoModal.hide();
                loadItensCotacao(formData.cotacao_id);
                limpaItensCotacao();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $("#loadingItensCotModal").hide();
                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        });
    });

    function limpaItensCotacao() {
        $("#itemCotacaoId").val(0);
        $("#product_id").val("");
        $("#marca_id").val("");
        $("#unidade").val("");
        $("#quantidade").val("");
        $("#observacao_item").val("");
    }

    $("#btnEnviaCot").on("click", function () {
        $("#loading").show();
        var row = $(this).closest("tr");
        var id = row.data("id");

        $.ajax({
            url: "cotacao-send",
            type: "POST",
            dataType: "json",
            data: { id: id },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                $("#loading").hide();
                alert(response.message);
                //console.log(response);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $("#loading").hide();
                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        });
    });

    function loadItensCotacao(cotacaoId) {
        $.ajax({
            url: "itens-cotacao-list",
            type: "POST",
            dataType: "json",
            data: { cotacao_id: cotacaoId },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                console.log(data);
                var tbody = $("#itensCotacaoTable");

                tbody.empty();
                var row = "";

                row =
                    '<table class="table table-bordered" id="itensCotacaoTable" style="width: 90%; margin-left: 5%">';
                row += "<thead>";
                row += '<tr style="background-color: #D3D3D3">';
                row += "<th>Produto</th>";
                row += "<th>Marca</th>";
                row += "<th>Qtd</th>";
                row += "<th>Unidade</th>";
                row += "<th>Observação</th>";
                row += "<th colspan='2'>Ações</th>";
                row += "</tr>";
                row += "</thead>";

                if (data.length === 0) {
                    row +=
                        '<tr><td colspan="7" class="text-center">Nenhum ítem cadastrado.</td></tr>';
                } else {
                    $.each(data, function (index, item) {
                        row += '<tr data-id="' + item.id + '">';
                        row += "<td>" + item.produto_nome + "</td>";
                        row += "<td>" + item.marca_nome + "</td>";
                        row += "<td>" + item.quantidade + "</td>";
                        row += "<td>" + getUnidade(item.unidade) + "</td>";
                        row += "<td>" + (item.observacao ? item.observacao : "") + "</td>";
                        row += "<td></td>";
                        row +=
                            '<td><nobr><button class="btn btn-sm btn-warning btnEditItem">Editar</button>';
                        row +=
                            '<button class="btn btn-sm btn-danger btnDeleteItem" style="margin-left: 8px">Excluir</button></nobr></td>';
                        row += "</tr>";
                    });
                }
                tbody.append(row);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        });
    }

    $("#itensCotacaoTable").on("click", ".btnEditItem", function () {
        var row = $(this).closest("tr");
        var itemId = row.data("id");

        $.ajax({
            url: "itens-cotacao-get",
            type: "POST",
            dataType: "json",
            data: { id: itemId },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                $("#itemCotacaoId").val(data.id);
                $("#itemCotacaoCotacaoId").val(data.cotacao_id);
                $("#product_id").val(data.product_id);
                $("#marca_id").val(data.marca_id);
                $("#quantidade").val(data.quantidade);
                $("#unidade").val(data.unidade);
                $("#observacao_item").val(data.observacao);
                $("#valor").val(data.valor);

                $("#itensCotacaoModalLabel").text("Editar Ítem Cotação");
                itensCotacaoModal.show();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        });
    });

    $("#itensCotacaoTable").on("click", ".btnDeleteItem", function () {
        if (!confirm("Tem certeza que deseja excluir este ítem?")) {
            return;
        }
        var row = $(this).closest("tr");
        var id = row.data("id");

        $.ajax({
            url: "itens-cotacao-destroy",
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

    $("#btnCloseItensCot").on("click", function () {
        itensCotacaoModal.hide();
    });

    $("#btnCancelItensCot").on("click", function () {
        itensCotacaoModal.hide();
    });

    // FornecedorCotacao modal and handlers

    var fornecedorCotacaoModal = new bootstrap.Modal(
        document.getElementById("fornecedorCotacaoModal")
    );

    $("#itensCotacaoTable").on("click", ".btnAddFornecedorCot", function () {
        var row = $(this).closest("tr");
        var itemId = row.data("id");
        var cotacaoId = $("#itemCotacaoCotacaoId").val();

        $("#fornecedorCotacaoForm")[0].reset();
        $("#fornecedorCotacaoId").val("0");
        $("#fornecedorCotacaoCotacaoId").val(cotacaoId);
        $("#fornecedorCotacaoItemId").val(itemId);
        $("#fornecedorCotacaoModalLabel").text("Adicionar Fornecedor Cotação");
        fornecedorCotacaoModal.show();
        loadFornecedorCotacao(itemId);
    });

    $("#fornecedorCotacaoForm").submit(function (e) {
        e.preventDefault();
        $("#loadingFornecedorCotModal").show();

        var formData = {
            id: $("#fornecedorCotacaoId").val(),
            cotacao_id: $("#fornecedorCotacaoCotacaoId").val(),
            item_id: $("#fornecedorCotacaoItemId").val(),
            fornecedor_id: $("#fornecedor_id").val(),
            valor_unitario: $("#valor_unitario").val(),
            valor_total: $("#valor_total").val(),
        };

        $.ajax({
            url: "fornecedor-cotacao-store",
            type: "POST",
            dataType: "json",
            data: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                $("#loadingFornecedorCotModal").hide();
                loadFornecedorCotacao(formData.item_id);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $("#loadingFornecedorCotModal").hide();
                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        });
    });

    function loadFornecedorCotacao(itemId) {
        $.ajax({
            url: "fornecedor-cotacao-list",
            type: "POST",
            dataType: "json",
            data: { item_id: itemId },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                var container = $("#fornecedorCotacaoTable");
                container.empty();

                var row =
                    '<table class="table table-bordered" style="width: 90%; margin-left: 5%">';
                row += "<thead>";
                row += '<tr style="background-color: #D3D3D3">';
                row += "<th>Fornecedor</th>";
                row += "<th>Valor Unitário</th>";
                row += "<th>Valor Total</th>";
                row += "<th>Ações</th>";
                row += "</tr>";
                row += "</thead>";

                if (data.length === 0) {
                    row +=
                        '<tr><td colspan="4" class="text-center">Nenhum fornecedor cadastrado.</td></tr>';
                } else {
                    $.each(data, function (index, fc) {
                        row += '<tr data-id="' + fc.id + '">';
                        row += "<td>" + fc.fornecedor_nome + "</td>";
                        row += "<td>" + fc.valor_unitario + "</td>";
                        row += "<td>" + fc.valor_total + "</td>";
                        row +=
                            '<td><button class="btn btn-sm btn-warning btnEditFornecedorCot">Editar</button>';
                        row +=
                            '<button class="btn btn-sm btn-danger btnDeleteFornecedorCot" style="margin-left: 8px">Excluir</button></td>';
                        row += "</tr>";
                    });
                }
                row += "</table>";
                container.append(row);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        });
    }

    $("#fornecedorCotacaoTable").on(
        "click",
        ".btnEditFornecedorCot",
        function () {
            var row = $(this).closest("tr");
            var fcId = row.data("id");

            $.ajax({
                url: "fornecedor-cotacao-get",
                type: "POST",
                dataType: "json",
                data: { id: fcId },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (data) {
                    $("#fornecedorCotacaoId").val(data.id);
                    $("#fornecedorCotacaoCotacaoId").val(data.cotacao_id);
                    $("#fornecedorCotacaoItemId").val(data.item_id);
                    $("#fornecedor_id").val(data.fornecedor_id);
                    $("#valor_unitario").val(data.valor_unitario);
                    $("#valor_total").val(data.valor_total);

                    $("#fornecedorCotacaoModalLabel").text(
                        "Editar Fornecedor Cotação"
                    );
                    fornecedorCotacaoModal.show();
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    for (i in XMLHttpRequest) {
                        if (i != "channel")
                            console.log(i + " : " + XMLHttpRequest[i]);
                    }
                },
            });
        }
    );

    $("#fornecedorCotacaoTable").on(
        "click",
        ".btnDeleteFornecedorCot",
        function () {
            if (!confirm("Tem certeza que deseja excluir este fornecedor?")) {
                return;
            }
            var row = $(this).closest("tr");
            var id = row.data("id");

            $.ajax({
                url: "fornecedor-cotacao-destroy",
                type: "POST",
                data: { id: id },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
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
        }
    );

    $("#btnCloseFornecedorCot").on("click", function () {
        fornecedorCotacaoModal.hide();
    });

    $("#btnCancelFornecedorCot").on("click", function () {
        fornecedorCotacaoModal.hide();
    });

    // Calculate valor_total on valor_unitario change
    $("#valor_unitario").on("input", function () {
        var valorUnitario = parseFloat($(this).val()) || 0;
        var quantidade = parseFloat($("#quantidade").val()) || 0;
        var valorTotal = valorUnitario * quantidade;
        $("#valor_total").val(valorTotal.toFixed(2));
    });

    // Update valor_total if quantidade changes in itensCotacao form
    $("#quantidade").on("input", function () {
        var valorUnitario = parseFloat($("#valor_unitario").val()) || 0;
        var quantidade = parseFloat($(this).val()) || 0;
        var valorTotal = valorUnitario * quantidade;
        $("#valor_total").val(valorTotal.toFixed(2));
    });
});

async function pesquisarCotacoes() {
    $("#searchCotacao").val("");
    $("#loading").show();
    $.ajax({
        url: "cotacoes-search",
        type: "POST",
        data: { query: "" },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        success: function (data) {
            $("#loading").hide();
            var tbody = $("#cotacaoTable tbody");
            tbody.empty();

            if (data.length === 0) {
                tbody.append(
                    '<tr><td colspan="5" class="text-center">Nenhuma cotação encontrada.</td></tr>'
                );
            } else {
                $.each(data, function (index, cotacao) {
                    var rowColor = index % 2 === 0 ? "#FOF8FF" : "#DCDCDC";
                    var row =
                        '<tr data-id="' +
                        cotacao.id +
                        '" style="background-color: ' +
                        rowColor +
                        '">' +
                        "<td>" +
                        cotacao.numero +
                        "</td>" +
                        "<td>" +
                        cotacao.encerramento +
                        "</td>" +
                        "<td>" +
                        cotacao.status +
                        "</td>" +
                        "<td>" +
                        cotacao.status_envio +
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

function getUnidade(p) {
    switch (p) {
        case 1:
            return "Kg";
            break;
        case 2:
            return "Cx";
            break;
        case 3:
            return "Unid";
            break;
        case 4:
            return "Saco";
            break;
        case 5:
            return "Metro";
            break;
    }
    
}
