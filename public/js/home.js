var path_img = ""; //producao path_img = '/public'
var justificativaModal = new bootstrap.Modal(
    document.getElementById("justificativaModal")
);
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
                $("#cotacoes").empty();
                var html = '<table class="table table-striped table-bordered">';
                html += "<tr>";
                html += '<th style="text-align: center">Número</th>';
                html += '<th style="text-align: center">Liberação</th>';
                html += '<th style="text-align: center">Operação</th>';
                html += "</tr>";
                if (data.length > 0) {
                    $.each(data, function (index, item) {
                        var dt = item.encerramento.split("T");
                        var dtlibera = new Date(item.encerramento);
                        const formatador = new Intl.DateTimeFormat("pt-BR", {
                            day: "2-digit",
                            month: "2-digit",
                            year: "numeric",
                            hour: "2-digit",
                            minute: "2-digit",
                        });
                        const dataFormatada = formatador.format(dtlibera);
                        var hora = dt[1].split(":");
                        var hora_libera = hora[0] + ":" + hora[1];
                        var dtcomp = dataFormatada;

                        html += '<tr data-cotacao-id="' + item.id + '">';
                        html +=
                            '   <td style="text-align: center">' +
                            item.numero +
                            "</td>";
                        html +=
                            '   <td style="text-align: center">' +
                            dtcomp +
                            "</td>";

                        if (item.status == 3) {
                            // If encerramento is past, disable the link or show as expired
                            html +=
                                '   <td style="text-align: center"><a href="javascript: void(0)" onclick="exibirCotacoes(' +
                                item.id +
                                ')" title="Visualizar"><i class="far fa-eye fa-2x"></i></a></td>';
                        } else {
                            html +=
                                '   <td style="text-align: center; color: gray; cursor: not-allowed;" title="Visualizar"><i class="far fa-eye fa-2x"></i></td>';
                        }
                        html += "</tr>";
                    });
                } else {
                    html +=
                        '<tr><td colspan="3" style="text-align: center">Não há cotações</td></tr>';
                }
                html += "</table>";
                $("#cotacoes").html(html);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $("#loadingItensCotModal").hide();
                for (i in XMLHttpRequest) {
                    if (i != "channel")
                        console.log(i + " : " + XMLHttpRequest[i]);
                }
            },
        });
    }

    loadCotacoes();

    // New code: onchange event handler for #cotacaoSelect

    // New code: fornecedor form submission handler
    $("#fornecedorForm").submit(function (e) {
        e.preventDefault();
        $("#loadingModal").show();

        var formData = {
            id: $("#fornecedorId").val(),
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
            categorias: $("#categorias").val(),
        };

        $.ajax({
            url: "admin/salvar-fornecedor",
            type: "POST",
            data: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                $("#loadingModal").hide();
                alert("Fornecedor salvo com sucesso.");
                fornecedorModal.hide();
                // Optionally reload or update fornecedor list here
                location.reload();
            },
            error: function (xhr, status, error) {
                $("#loadingModal").hide();
                alert("Erro ao salvar fornecedor: " + error);
            },
        });
    });
});

async function exibirCotacoes(id) {
    var cotacaoId = id;
    var grid = $(".grid_cotacoes");
    grid.empty();

    if (!cotacaoId) {
        // No cotacao selected, clear grid and return
        return;
    }

    // Remove highlight from previously selected row
    $("#cotacoes tr.highlight-row").removeClass("highlight-row");

    // Add highlight to the clicked row
    $('#cotacoes tr[data-cotacao-id="' + cotacaoId + '"]').addClass(
        "highlight-row"
    );

    // Show loading indicator
    $("#loading").show();

    // Fetch cotacao items
    $.ajax({
        url: "itens-cotacao",
        type: "POST",
        dataType: "json",
        data: { cotacaoId: cotacaoId },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (items) {
            if (items.length === 0) {
                grid.html("<p>Nenhum item encontrado para esta cotação.</p>");
                $("#loading").hide();
                return;
            }

            // For each item, fetch fornecedor_cotacao data
            var itemsProcessed = 0;
            var html = '<div class="accordion" id="cotacaoAccordion">';
            var i = 1;
            var x = 1;
            items.forEach(function (item, index) {
                // Fetch fornecedor_cotacao for this item
                $.ajax({
                    url: "fornecedor-cotacao-list",
                    type: "POST",
                    dataType: "json",
                    data: { item_id: item.id },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },

                    success: function (fornecedores) {
                        //console.log(fornecedores);
                        // Build HTML for this item and its fornecedores
                        html += '<div class="accordion-item">';
                        html +=
                            '   <table class="table table-striped table-bordered" style="border: 2px solid; border-radius: 30px;">';
                        html += "       <tr>";
                        html +=
                            '           <td style="width: 50%"><b>Produto:</b> <span class="btn btn-primary">' +
                            x +
                            "</span> " +
                            (item.produto_nome || "") +
                            "</td>";
                        html +=
                            '           <td style="width: 50%"><b>Quantidade:</b> ' +
                            (item.quantidade || "") +
                            "</td>";
                        html += "       </tr>";
                        html += "       <tr>";
                        html +=
                            '           <td style="width: 50%"><b>Unidade:</b> ' +
                            (item.unidade || "") +
                            "</td>";
                        html += "       </tr>";
                        html += "       <tr>";
                        html += '           <td colspan="2">';
                        html += '               <div class="accordion-body">';
                        html +=
                            '                   <table class="table table-striped table-bordered">';
                        html +=
                            "                       <thead><tr><th></th><th>Empresa</th><th>Unitário</th><th>Total</th><th>Marca</th><th>Observação</th></tr></thead><tbody>";
                        if (fornecedores.length === 0) {
                            html +=
                                '                   <tr><td colspan="5">Nenhum fornecedor para este item.</td></tr>';
                        } else {
                            fornecedores.forEach(function (forn) {
                                var staprovado =
                                    forn.st_aprovado > 0
                                        ? "ativo.gif"
                                        : "inativo.gif";
                                var tpfrete =
                                    forn.tipo_frete == 1 ? "FOB" : "SIF";
                                var val_frete =
                                    forn.valor_frete > 0
                                        ? " - " +
                                          number_format(
                                              forn.valor_frete,
                                              2,
                                              ",",
                                              "."
                                          )
                                        : "";
                                var fat_minimo =
                                    forn.faturamento_minimo > 0
                                        ? number_format(
                                              forn.faturamento_minimo,
                                              2,
                                              ",",
                                              "."
                                          )
                                        : "";
                                html += "<tr>";
                                html +=
                                    '   <td align="center" id="td_' +
                                    forn.item_id +
                                    "_" +
                                    i +
                                    '">';
                                html +=
                                    '       <a href="javascript: void(0)" style="text-decoration: none" onclick="aprovarItem(' +
                                    i +
                                    ", " +
                                    forn.id +
                                    ", " +
                                    forn.cotacao_id +
                                    ", " +
                                    forn.item_id +
                                    ')">';
                                html +=
                                    '           <img src="' +
                                    path_img +
                                    "/img/" +
                                    staprovado +
                                    '" id="img' +
                                    forn.item_id +
                                    "_" +
                                    i +
                                    '">';
                                html += "       </a>";
                                html += "   </td>";
                                html +=
                                    '   <td title="Faturamento mínimo da empresa: ' +
                                    fat_minimo +
                                    ". Frete: " +
                                    tpfrete +
                                    val_frete +
                                    '">' +
                                    (forn.fornecedor_nome || "") +
                                    "</td>";
                                //html += '   <td>' + fat_minimo +'</td>';
                                html +=
                                    "   <td>" +
                                    number_format(
                                        forn.valor_unitario,
                                        2,
                                        ",",
                                        "."
                                    ) +
                                    "</td>";
                                html +=
                                    "   <td>" +
                                    number_format(
                                        forn.valor_total,
                                        2,
                                        ",",
                                        "."
                                    ) +
                                    "</td>";
                                //html += '   <td>' + tpfrete + val_frete +'</td>';
                                html +=
                                    "   <td>" + (forn.marca || "") + "</td>";
                                html +=
                                    "   <td>" +
                                    (forn.observacao || "") +
                                    "</td>";
                                html += "</tr>";
                                i++;
                            });
                        }
                        html +=
                            "</tbody></table></div></td></tr></table></div>";
                        itemsProcessed++;
                        x++;

                        if (itemsProcessed === items.length) {
                            html +=
                                '<table class="table table-striped table-bordered"><tr><td align="right"><input type="button" class="btn btn-success" id="btnAprovaCotacao" value="Aprovar Cotação"></td></tr></table>';
                            html += "</div>"; // close accordion
                            grid.html(html);
                            $("#loading").hide();

                            // Add click event handler for Aprovar Cotação button
                            $("#btnAprovaCotacao").click(function () {
                                //var cotacaoId = cotacaoId;
                                if (!cotacaoId) {
                                    alert(
                                        "Selecione uma cotação antes de aprovar."
                                    );
                                    return;
                                }
                                $("#loading").show();
                                $.ajax({
                                    url: "cotacao-aprovar",
                                    type: "POST",
                                    dataType: "json",
                                    data: { cotacao_id: cotacaoId },
                                    headers: {
                                        "X-CSRF-TOKEN": $(
                                            'meta[name="csrf-token"]'
                                        ).attr("content"),
                                    },
                                    success: function (response) {
                                        $("#loading").hide();
                                        if (response.success) {
                                            alert(response.message);
                                            window.location.href = "/home";
                                            // Optionally reload or update UI here
                                        } else {
                                            alert("Erro: " + response.message);
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        $("#loading").hide();
                                        alert(
                                            "Erro ao aprovar cotação: " + error
                                        );
                                    },
                                });
                            });
                        }
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        itemsProcessed++;
                        if (itemsProcessed === items.length) {
                            html += "</div>"; // close accordion
                            grid.html(html);
                            $("#loading").hide();
                        }

                        for (i in XMLHttpRequest) {
                            if (i != "channel")
                                console.log(i + " : " + XMLHttpRequest[i]);
                        }
                    },
                });
            });
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            grid.html("<p>Erro ao carregar itens da cotação.</p>");
            $("#loading").hide();
            for (i in XMLHttpRequest) {
                if (i != "channel") console.log(i + " : " + XMLHttpRequest[i]);
            }
        },
    });
}

var pendingJustification = null;

async function aprovarItem(i, id, cotacao_id, item_id) {
    $("#loading").show();
    var url = "aprovar-item";
    var img = document.getElementById("img" + item_id + "_" + i);
    var valor;
    if (img.src.indexOf("inativo.gif") !== -1) {
        // item inativo
        valor = 1; // ativa
    } else {
        valor = 0; // inativa
    }

    // Check if valor_unitario is the lowest for this item_id in the cotacao
    // We need to get all valor_unitario for this item_id and cotacao_id from the DOM or fetch from server
    // For simplicity, we will fetch from server synchronously here

    $.ajax({
        url: "fornecedor-cotacao-list",
        type: "POST",
        dataType: "json",
        data: { item_id: item_id },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (fornecedores) {
            var currentFornecedor = fornecedores.find((f) => f.id === id);
            if (!currentFornecedor) {
                alert("Fornecedor não encontrado.");
                $("#loading").hide();
                return;
            }
            var minValor = Math.min(
                ...fornecedores.map((f) => parseFloat(f.valor_unitario))
            );
            if (valor === 1 && currentFornecedor.valor_unitario > minValor) {
                // Show justificativa modal
                pendingJustification = { i, id, cotacao_id, item_id, valor };
                $("#justificativaItemIndex").val(i);
                $("#justificativaFornecedorId").val(id);
                $("#justificativaCotacaoId").val(cotacao_id);
                $("#justificativaItemId").val(item_id);
                // Clear and load justificativas into select
                $("#selectJustificativa")
                    .empty()
                    .append(
                        '<option value="">Selecione uma justificativa</option>'
                    );
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "load-justificativas",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (r) {
                        $.each(r, function (index, item) {
                            $("#selectJustificativa").append(
                                $("<option>", {
                                    value: item.id,
                                    text: item.descricao,
                                })
                            );
                        });
                        justificativaModal.show();
                        $("#loading").hide();
                    },
                    /*
                    error: function () {
                        alert("Erro ao carregar justificativas.");
                        $("#loading").hide();
                    },*/
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        $("#loading").hide();
                        alert("Erro ao carregar justificativas.");
                        for (i in XMLHttpRequest) {
                            if (i != "channel")
                                console.log(i + " : " + XMLHttpRequest[i]);
                        }
                    },
                });
            } else {
                // Proceed with approval without justification
                sendApproval({ i, id, cotacao_id, item_id, valor });
            }
        },
        error: function () {
            alert("Erro ao verificar valores para aprovação.");
            $("#loading").hide();
        },
    });
}

function sendApproval({
    i,
    id,
    cotacao_id,
    item_id,
    valor,
    justificativa = null,
}) {
    var url = "aprovar-item";
    var data = {
        id: id,
        cotacao_id: cotacao_id,
        item_id: item_id,
        valor: valor,
    };
    if (justificativa) {
        data.justificativa = justificativa;
    }

    $.ajax({
        type: "POST",
        dataType: "json",
        url: url,
        data: data,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (r) {
            console.log(r);
            $("#loading").hide();
            if (r == 1) {
                var img = document.getElementById("img" + item_id + "_" + i);
                if (img.src.indexOf("inativo.gif") !== -1) {
                    img.src = path_img + "/img/ativo.gif";
                    if (valor === 1) {
                        var imgs = document.querySelectorAll(
                            'img[id^="img' + item_id + '_"]'
                        );
                        imgs.forEach(function (otherImg) {
                            if (otherImg.id !== "img" + item_id + "_" + i) {
                                otherImg.src = path_img + "/img/inativo.gif";
                            }
                        });
                    }
                } else {
                    img.src = path_img + "/img/inativo.gif";
                }
                justificativaModal.hide();
            } else {
                alert("Erro ao aprovar item!");
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $("#loading").hide();
            alert("Erro ao aprovar item!");
            for (i in XMLHttpRequest) {
                if (i != "channel") console.log(i + " : " + XMLHttpRequest[i]);
            }
        },
    });
}

$(document).ready(function () {
    $("#btnSubmitJustificativa").click(function () {
        var selectedJustificativaId = $("#selectJustificativa").val();
        if (!selectedJustificativaId) {
            alert("Por favor, selecione uma justificativa.");
            return;
        }
        if (!pendingJustification) {
            alert("Erro interno: dados da justificativa não encontrados.");
            return;
        }
        $("#loading").show();
        sendApproval({
            ...pendingJustification,
            justificativa: selectedJustificativaId,
        });
        pendingJustification = null;
    });
});

function setFormaPg(p) {
    var ret = "";
    switch (p) {
        case 1:
            ret = "Pagamento à vista";
            break;
        case 2:
            ret = "30 dias";
            break;
        case 3:
            ret = "60 dias";
            break;
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
