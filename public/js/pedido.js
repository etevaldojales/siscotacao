var path = ""; // local; producao: "/public"

$(document).ready(function () {
    $("#valor").mask("000.000.000.000.000,00", { reverse: true });

    $("#btnClose").on("click", function () {
        itemsPedidoModal.hide();
    });

    $("#btnCancel").on("click", function () {
        itemsPedidoModal.hide();
    });

    var itemsPedidoModal = new bootstrap.Modal(
        document.getElementById("itensPedidoModal")
    );

    function loadPedidos() {
        $("#loading").show();
        $.ajax({
            url: "pedidos",
            method: "GET",
            dataType: "json",
            success: function (data) {
                //console.log(data)
                var tbody = "";
                data.forEach(function (pedido) {
                    var statusText = "";
                    switch (pedido.status) {
                        case 1:
                            statusText = "Gerado";
                            break;
                        case 2:
                            statusText = "Enviado";
                            break;
                        case 3:
                            statusText = "Cancelado";
                            break;
                        case 4:
                            statusText = "Recebido";
                            break;
                        case 5:
                            statusText = "Aprovado";
                            break;
                    }
                    tbody += "<tr>";
                    tbody += "<td>" + pedido.id + "</td>";
                    tbody +=
                        "<td>" +
                        (pedido.usuario ? pedido.usuario.name : "") +
                        "</td>";
                    tbody +=
                        "<td>" +
                        (pedido.fornecedor
                            ? pedido.fornecedor.razao_social
                            : "") +
                        "</td>";
                    tbody += "<td>" + pedido.numero + "</td>";
                    tbody +=
                        "<td>" +
                        number_format(pedido.valor, 2, ",", ".") +
                        "</td>";
                    //tbody +=     "<td>" + (pedido.actived ? "Sim" : "Não") + "</td>";
                    tbody += "<td>" + statusText + "</td>";
                    tbody +=
                        "<td><nobr>" +
                        '<button class="btn btn-primary btn-sm update-status" data-id="' +
                        pedido.id +
                        '">Atualizar Status</button>';
                     tbody += '<button class="btn btn-success btn-sm visualizarPedidoBtn" style="margin-left: 8px;" data-id="' +
                        pedido.id +
                        '">Visualizar</button>';

                    if (pedido.status == 1) {
                        tbody +=
                            '<button class="btn btn-success btn-sm btnAddItemPedido" style="margin-left: 8px;">Adicionar ítens</button> ';
                        tbody +=
                            '<button class="btn btn-warning btn-sm editPedidoBtn" style="margin-left: 8px;" data-id="' +
                            pedido.id +
                            '">Editar Pedido</button> ';
                    }
                    tbody += "</nobr></td>";
                    tbody += "</tr>";
                });
                $("#pedidos-table tbody").html(tbody);
                $("#loading").hide();
            },
        });
    }

    // AJAX search for pedidos
    $("#searchPedido").on("input", function () {
        var query = $(this).val();
        $("#loadingSearch").show();

        $.ajax({
            url: "pedidos-search",
            type: "POST",
            data: { query: query },
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                $("#loadingSearch").hide();
                var tbody = "";
                if (data.length === 0) {
                    tbody = '<tr><td colspan="7" class="text-center">Nenhum pedido encontrado.</td></tr>';
                } else {
                    data.forEach(function (pedido, index) {
                        var statusText = "";
                        switch (pedido.status) {
                            case 1:
                                statusText = "Gerado";
                                break;
                            case 2:
                                statusText = "Enviado";
                                break;
                            case 3:
                                statusText = "Cancelado";
                                break;
                            case 4:
                                statusText = "Recebido";
                                break;
                            case 5:
                                statusText = "Aprovado";
                                break;
                        }
                        tbody += "<tr>";
                        tbody += "<td>" + pedido.id + "</td>";
                        tbody += "<td>" + (pedido.usuario ? pedido.usuario.name : "") + "</td>";
                        tbody += "<td>" + (pedido.fornecedor ? pedido.fornecedor.razao_social : "") + "</td>";
                        tbody += "<td>" + pedido.numero + "</td>";
                        tbody += "<td>" + number_format(pedido.valor, 2, ",", ".") + "</td>";
                        tbody += "<td>" + statusText + "</td>";
                        tbody +=
                            "<td><nobr>" +
                            '<button class="btn btn-primary btn-sm update-status" data-id="' +
                            pedido.id +
                            '">Atualizar Status</button>';
                        if (pedido.status == 1) {
                            tbody +=
                                '<button class="btn btn-success btn-sm btnAddItemPedido" style="margin-left: 8px;">Adicionar ítens</button> ';
                            tbody +=
                                '<button class="btn btn-warning btn-sm editPedidoBtn" style="margin-left: 8px;" data-id="' +
                                pedido.id +
                                '">Editar Pedido</button> ';
                        }
                        tbody += "</nobr></td>";
                        tbody += "</tr>";
                    });
                }
                $("#pedidos-table tbody").html(tbody);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $("#loadingSearch").hide();
                for (i in XMLHttpRequest) {
                    if (i != "channel") console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        });
    });

    // Buscar Todos button handler
    window.pesquisarPedidos = function () {
        $("#searchPedido").val("");
        $("#loadingSearch").show();
        $.ajax({
            url: "pedidos-search",
            type: "POST",
            data: { query: "" },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "json",
            success: function (data) {
                $("#loadingSearch").hide();
                var tbody = "";
                if (data.length === 0) {
                    tbody = '<tr><td colspan="7" class="text-center">Nenhum pedido encontrado.</td></tr>';
                } else {
                    data.forEach(function (pedido, index) {
                        var statusText = "";
                        switch (pedido.status) {
                            case 1:
                                statusText = "Gerado";
                                break;
                            case 2:
                                statusText = "Enviado";
                                break;
                            case 3:
                                statusText = "Cancelado";
                                break;
                            case 4:
                                statusText = "Recebido";
                                break;
                            case 5:
                                statusText = "Aprovado";
                                break;
                        }
                        tbody += "<tr>";
                        tbody += "<td>" + pedido.id + "</td>";
                        tbody += "<td>" + (pedido.usuario ? pedido.usuario.name : "") + "</td>";
                        tbody += "<td>" + (pedido.fornecedor ? pedido.fornecedor.razao_social : "") + "</td>";
                        tbody += "<td>" + pedido.numero + "</td>";
                        tbody += "<td>" + number_format(pedido.valor, 2, ",", ".") + "</td>";
                        tbody += "<td>" + statusText + "</td>";
                        tbody +=
                            "<td><nobr>" +
                            '<button class="btn btn-primary btn-sm update-status" data-id="' +
                            pedido.id +
                            '">Atualizar Status</button>';
                        if (pedido.status == 1) {
                            tbody +=
                                '<button class="btn btn-success btn-sm btnAddItemPedido" style="margin-left: 8px;">Adicionar ítens</button> ';
                            tbody +=
                                '<button class="btn btn-warning btn-sm editPedidoBtn" style="margin-left: 8px;" data-id="' +
                                pedido.id +
                                '">Editar Pedido</button> ';
                        }
                        tbody += "</nobr></td>";
                        tbody += "</tr>";
                    });
                }
                $("#pedidos-table tbody").html(tbody);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $("#loadingSearch").hide();
                for (i in XMLHttpRequest) {
                    if (i != "channel") console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        });
    };

    loadPedidos();
    cargaInicioPedido();

    $(document).on("click", ".update-status", function () {
        var pedidoId = $(this).data("id");
        var newStatus = prompt(
            "Digite o novo status (1: Gerado, 2: Enviado, 3: Cancelado, 4: Recebido, 5: Aprovado):"
        );
        if (newStatus && [1, 2, 3, 4, 5].includes(parseInt(newStatus))) {
            $("#loading").show();
            $.ajax({
                url: "pedidos/" + pedidoId + "/status",
                method: "PATCH",
                data: {
                    status: newStatus,
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    $("#loading").hide();
                    alert(response.message);
                    loadPedidos();
                },
                error: function (xhr) {
                    $("#loading").hide();
                    alert("Erro ao atualizar status");
                },
            });
        } else {
            alert("Status inválido");
        }
    });

    // Visualizar button click handler
    $(document).on("click", ".visualizarPedidoBtn", function () {
        var pedidoId = $(this).data("id");
        window.location.href += "/" + pedidoId + "/show";
    });

    // Open modal on button click
    function cargaInicioPedido() {
        $("#pedidoForm")[0].reset();
        $("#formErrors").html("");

        // Clear order items table
        $("#pedidoItemsTable tbody").empty();

        // Fetch fornecedores and populate select
        $.ajax({
            url: "fornecedores/active",
            method: "GET",
            dataType: "json",
            success: function (data) {
                var select = $("#id_fornecedor");
                select.empty();
                select.append(
                    '<option value="">Selecione um fornecedor</option>'
                );
                data.forEach(function (fornecedor) {
                    select.append(
                        '<option value="' +
                            fornecedor.id +
                            '">' +
                            fornecedor.razao_social +
                            "</option>"
                    );
                });
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert("Erro ao carregar fornecedores");

                $("#loading").hide();
                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        });

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
        $("#num_pedido").val(uniqueNumber);
    }

    // Submit form via AJAX
    $("#pedidoForm").submit(function (e) {
        e.preventDefault();
        $("#loading").show();
        $("#formErrors").html("");

        var itens = [];
        $("#pedidoItemsTable tbody tr").each(function () {
            var product_id = $(this).find(".product_id").val();
            var marca_id = $(this).find(".marca_id").val();
            var quantidade = $(this).find(".quantidade").val();
            var peso = $(this).find(".peso").val();
            var unidade = $(this).find(".unidade").val();
            var valor = $(this).find(".valor").val();
        });

        var valor_pedido = $("#valor").val().replace(".", "");
        valor_pedido = valor_pedido.replace(",", ".");
        var forma_pagamento = $('#forma_pagamento').val();
        var prazo_entrega = $('#prazo_entrega').val();
        var tipo_frete = parseInt($('#tipo_frete').val());
        var valor_frete = $('#valor_frete').val().replace('.', '');
        valor_frete = valor_frete.replace(',', '.');
        valor_frete = parseFloat(valor_frete) > 0 ? parseFloat(valor_frete) : 0;
        var observacao = $('#observacao').val();


        var formData = {
            num_pedido: $("#num_pedido").val(),
            id_usuario: $("#id_usuario").val(),
            id_fornecedor: $("#id_fornecedor").val(),
            valor: valor_pedido,
            forma_pagamento: forma_pagamento,
            prazo_entrega: prazo_entrega,
            tipo_frete: tipo_frete,
            valor_frete: valor_frete,
            observacao: observacao,
            actived: $("#actived").val(),
            status: $("#status").val(),
        };

        var pedidoId = $("#num_pedido").data("pedido-id"); // custom data attribute to track editing

        var ajaxOptions = {
            url: "salvar-pedidos",
            method: "POST",
            data: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                $("#loading").hide();
                alert(response.message);
                loadPedidos();
                $("#pedidoForm")[0].reset();
                $("#num_pedido").removeData("pedido-id");
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $("#loading").hide();
                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        };

        if (pedidoId) {
            ajaxOptions.url = "pedidos/" + pedidoId;
            ajaxOptions.method = "PATCH";
        }

        $.ajax(ajaxOptions);
    });

    // Edit pedido button click
    $(document).on("click", ".editPedidoBtn", function () {
        var pedidoId = $(this).data("id");
        $("#loading").show();
        $.ajax({
            url: "pedido-get",
            method: "POST",
            dataType: "json",
            data: { id: pedidoId },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                //console.log(response);
                $("#loading").hide();
                var pedido = response;

                if (pedido) {
                    $("#num_pedido")
                        .val(pedido.numero)
                        .data("pedido-id", pedido.id);
                    $("#id_fornecedor").val(pedido.id_fornecedor);
                    $("#valor").val(pedido.valor);
                    $("#actived").val(pedido.actived ? "1" : "0");
                    $("#status").val(pedido.status);
                }
            },
            error: function () {
                $("#loading").hide();
                alert("Erro ao carregar pedido");
            },
        });
    });

    function formatCurrency(value) {
        value = value.replace(/\D/g, ""); // Remove tudo que não for dígito
        value = (value / 100).toFixed(2) + ""; // Divide por 100 e fixa 2 casas decimais
        value = value.replace(".", ","); // Substitui ponto por vírgula
        value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1."); // Adiciona os pontos de milhar
        return value;
    }

    function applyMask(input) {
        input.value = formatCurrency(input.value);
    }

    function number_format(number, decimals, dec_point, thousands_sep) {
        // Strip all characters but numerical ones.
        number = (number + "").replace(/[^0-9+\-Ee.]/g, "");
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = typeof thousands_sep === "undefined" ? "," : thousands_sep,
            dec = typeof dec_point === "undefined" ? "." : dec_point,
            s = "",
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return "" + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : "" + Math.round(n)).split(".");
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || "").length < prec) {
            s[1] = s[1] || "";
            s[1] += new Array(prec - s[1].length + 1).join("0");
        }
        return s.join(dec);
    }
});

function setValorFrete(p) {
    if(p == 1) {
        document.getElementById('dv_val_frete').style.display = '';
    }
    else {
        document.getElementById('dv_val_frete').style.display = 'none';
        $('#valor_frete').val(0); 
    }
} 

