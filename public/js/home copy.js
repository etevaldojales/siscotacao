$(document).ready(function () {
    var fornecedorModal = new bootstrap.Modal(
        document.getElementById("fornecedorModal")
    );

    function loadCotacoes() {
        $.ajax({
            url: "cotacoes-active",
            type: "GET",
            dataType: "json",
            success: function (data) {
                var select = $("#cotacaoSelect");
                select.empty();
                if (data.length > 0) {
                    select.append(
                        '<option value="">Selecione uma cotação</option>'
                    );
                    $.each(data, function (index, cotacao) {
                        select.append(
                            '<option value="' +
                                cotacao.id +
                                '">' +
                                cotacao.numero +
                                "</option>"
                        );
                    });
                } else {
                    select.append(
                        '<option value="">Nenhuma cotação ativa encontrada</option>'
                    );
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                var select = $("#cotacaoSelect");
                select.empty();
                select.append(
                    '<option value="">Erro ao carregar cotações</option>'
                );

                $("#loadingItensCotModal").hide();
                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        });
    }

    loadCotacoes();

    $("#cotacaoSelect").on("change", function () {
        var cotacaoId = $(this).val();
        if (cotacaoId) {
            $("#loading").show();
            $.ajax({
                url: "fornecedor-cotacoes",
                type: "POST",
                dataType: "json",
                data: { cotacaoId: cotacaoId },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (data) {
                    var grid = $(".grid_cotacoes");
                    grid.empty();

                    if (data.length > 0) {
                        var table = $(
                            '<table class="table table-striped table-bordered"></table>'
                        );
                        var thead = $(
                            "<thead><tr><th>Fornecedor</th><th>Valor Total</th><th>Ações</th></tr></thead>"
                        );
                        table.append(thead);

                        var tbody = $("<tbody></tbody>");
                        $.each(data, function (index, item) {
                            var tr = $("<tr></tr>");
                            tr.append("<td>" + item.razao_social + "</td>");
                            tr.append(
                                "<td>" +
                                    number_format(
                                        item.total_valor,
                                        2,
                                        ",",
                                        "."
                                    ) +
                                    "</td>"
                            );
                            var actions = $("<td></td>");
                            var btnVisualizar = $(
                                '<button class="btn btn-primary btn-sm mr-2">Visualizar</button>'
                            );
                            var btnAprovar = $(
                                '<button class="btn btn-success btn-sm">Aprovar</button>'
                            );

                            // Add click handlers for buttons if needed
                            btnVisualizar.on("click", function () {
                                $("#loading").show();
                                var cotacaoId = $("#cotacaoSelect").val();
                                var fornecedorId = item.fornecedor_id;

                                // Fetch fornecedor details and fornecedor_cotacao data
                                $.ajax({
                                    url: "fornecedor-details",
                                    data: {
                                        cotacaoId: cotacaoId,
                                        fornecedorId: fornecedorId,
                                    },
                                    headers: {
                                        "X-CSRF-TOKEN": $(
                                            'meta[name="csrf-token"]'
                                        ).attr("content"),
                                    },
                                    type: "POST",
                                    dataType: "json",
                                    success: function (response) {
                                        $("#loading").hide();
                                        // Populate modal fields
                                        $("#fornecedorRazaoSocial").text(response.fornecedor.razao_social);
                                        $("#fornecedorCNPJ").text(response.fornecedor.cnpj);
                                        $("#fornecedorTelefone").text(response.fornecedor.telefone);
                                        $("#fornecedorEmail").text(response.fornecedor.email);
                                        $("#formaPagamento").text(setFormaPg(response.fornecedor_cotacoes[0].forma_pagamento));
                                        $("#prazoEntrega").text(response.fornecedor_cotacoes[0].prazo_entrega);

                                        // Populate fornecedor cotacoes grid
                                        var grid = $("#fornecedorCotacoesGrid");
                                        grid.empty();

                                        if (response.fornecedor_cotacoes.length > 0) {
                                            var table = $(
                                                '<table class="table table-striped table-bordered"></table>'
                                            );
                                            var thead = $(
                                                "<thead><tr><th>Produto</th><th>Quantidade</th><th>Valor Unitário</th><th>Valor Total</th></tr></thead>"
                                            );
                                            table.append(thead);

                                            var tbody = $("<tbody></tbody>");
                                            $.each(response.fornecedor_cotacoes, function (index, cotacaoItem) {
                                                    var tr = $("<tr></tr>");
                                                    tr.append(
                                                        "<td>" +
                                                            cotacaoItem.product_name +
                                                            "</td>"
                                                    );
                                                    tr.append(
                                                        "<td>" +
                                                            cotacaoItem.itens_cotacao_quantidade +
                                                            "</td>"
                                                    );
                                                    tr.append(
                                                        "<td>" +
                                                            number_format(
                                                                cotacaoItem.valor_unitario,
                                                                2,
                                                                ",",
                                                                "."
                                                            ) +
                                                            "</td>"
                                                    );
                                                    tr.append(
                                                        "<td>" +
                                                            number_format(
                                                                cotacaoItem.valor_total,
                                                                2,
                                                                ",",
                                                                "."
                                                            ) +
                                                            "</td>"
                                                    );
                                                    tbody.append(tr);
                                                }
                                            );
                                            table.append(tbody);
                                            grid.append(table);
                                        } else {
                                            grid.append(
                                                "<p>Nenhum item encontrado para este fornecedor.</p>"
                                            );
                                        }

                                        // Show the modal
                                        fornecedorModal.show();
                                    },
                                    error: function (
                                        XMLHttpRequest,
                                        textStatus,
                                        errorThrown
                                    ) {
                                        $("#loading").hide();
                                        alert(
                                            "Erro ao carregar detalhes do fornecedor."
                                        );

                                        $("#loadingItensCotModal").hide();
                                        for (i in XMLHttpRequest) {
                                            if (i != "channel")
                                                console.log(
                                                    i +
                                                        " : " +
                                                        XMLHttpRequest[i]
                                                );
                                        }
                                    },
                                });
                            });
                            btnAprovar.on("click", function () {
                                $("loading").show();
                                var cotacaoId = $("#cotacaoSelect").val();
                                var fornecedorId = item.fornecedor_id;

                                if (!cotacaoId || !fornecedorId) {
                                    alert("Cotação ou fornecedor inválido.");
                                    return;
                                }

                                if (
                                    !confirm(
                                        "Confirma a aprovação da cotação para o fornecedor " +
                                            item.razao_social +
                                            "?"
                                    )
                                ) {
                                    return;
                                }

                                $.ajax({
                                    url: "cotacao-aprovar",
                                    type: "POST",
                                    dataType: "json",
                                    data: {
                                        cotacao_id: cotacaoId,
                                        fornecedor_id: fornecedorId,
                                        _token: $(
                                            'meta[name="csrf-token"]'
                                        ).attr("content"),
                                    },
                                    headers: {
                                        "X-CSRF-TOKEN": $(
                                            'meta[name="csrf-token"]'
                                        ).attr("content"),
                                    },
                                    success: function (response) {
                                        $("loading").hide();
                                        alert(response.message);
                                        if (response.success) {
                                            // Optionally reload or update the UI
                                            $("#cotacaoSelect").trigger(
                                                "change"
                                            );
                                        }
                                    },
                                    error: function (xhr) {
                                        $("loading").hide();
                                        alert(
                                            "Erro ao aprovar cotação: " +
                                                xhr.responseText
                                        );
                                    },
                                });
                            });

                            actions.append(btnVisualizar);
                            actions.append(btnAprovar);
                            tr.append(actions);
                            tbody.append(tr);
                        });
                        table.append(tbody);
                        grid.append(table);
                    } else {
                        grid.append(
                            "<p>Nenhuma resposta encontrada para esta cotação.</p>"
                        );
                    }
                    $("#loading").hide();
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    $("loading").hide();
                    var grid = $(".grid_cotacoes");
                    grid.empty();
                    grid.append(
                        "<p>Erro ao carregar respostas do fornecedor.</p>"
                    );
                    for (i in XMLHttpRequest) {
                        if (i != "channel")
                            console.log(i + " : " + XMLHttpRequest[i]);
                    }
                },
            });
        } else {
            $(".grid_cotacoes").empty();
        }
    });

    function setFormaPg(p) {
        var ret = "";
        switch (p) {
            case 1:
                ret = "Pagamento à vista";
                break;
            case 2:
                ret =  "30 dias";
                break;
            case 3:
                ret = "60 dias";
                break;4
        }
        return ret;
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
