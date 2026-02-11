// items pedido
$("#valor_unitario").mask("000.000.000.000.000,00", { reverse: true });

$("#btnClose").on("click", function () {
    itemsPedidoModal.hide();
    window.location.reload()
});

$("#btnCancel").on("click", function () {
    itemsPedidoModal.hide();
    window.location.reload()
});

var itemsPedidoModal = new bootstrap.Modal(
    document.getElementById("itensPedidoModal")
);

// Reload parent page when modal is fully hidden
$("#itensPedidoModal").on("hidden.bs.modal", function () {
    window.parent.location.reload();
});

// Remove item button click
$(document).on("click", ".removeItemBtn", function () {
    var itemId = $(this).data("id");
    var row = $(this).closest("tr");

    if (itemId) {
        if (confirm("Tem certeza que deseja remover este item?")) {
            $.ajax({
                url: "remover-item-pediido",
                method: "POST",
                data: {
                    id: itemId,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    if (response.success) {
                        row.remove();
                        alert("Item removido com sucesso");
                    } else {
                        alert("Erro ao remover item");
                    }
                },
                error: function () {
                    alert("Erro ao remover item");
                },
            });
        }
    } else {
        row.remove();
    }
});

function carregarMarcasProduto(produtoId) {
    if (produtoId) {
        const productSelect = document.getElementById("product_id");
        const marcaSelect = document.getElementById("marca_id");

        // Clear marca options
        marcaSelect.innerHTML = '<option value="">Selecione a marca</option>';

        if (produtoId) {
            $.ajax({
                type: "POST",
                url: "marcas-produto",
                data: { id_produto: produtoId },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    $.each(response, function (index, item) {
                        marcaSelect.innerHTML +=
                            '<option value="' +
                            item.id +
                            '">' +
                            item.nome +
                            "</option>";
                    });
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    for (i in XMLHttpRequest) {
                        if (i != "channel")
                            console.log(i + " : " + XMLHttpRequest[i]);
                    }
                },
            });
        }
    }
}

// Load pedido items
function loadPedidoItems(pedidoId) {
    $.ajax({
        url: "list-pedidos-itens",
        method: "POST",
        data: { pedido_id: pedidoId },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            var tbodyi = "";
            if (data) {
                data.forEach(function (item) {
                    tbodyi +=
                        "<tr>" +
            "<td>" +
            item.produto_nome +
            "</td>" +
            "<td>" +
            item.marca_nome +
            "</td>" +
            "<td>" +
            item.quantidade +
            "</td>" +
            "<td>" +
            new Intl.NumberFormat("pt-BR", {
                style: "currency",
                currency: "BRL",
            }).format(item.valor_total) +
            "</td>" +
            "<td>" +
            '<nobr><button class="btn btn-sm btn-primary editItemBtn" data-id="' +
            item.id +
            '">Editar</button> ' +
            '<button class="btn btn-sm btn-danger removeItemBtn" data-id="' +
            item.id +
            '">Remover</button></nobr>' +
            "</td>" +
            "</tr>";
                });
            } else {
                tbodyi +=
                    "<tr>" +
                    "<td colspan='6'>Nenhum item cadastrado nesse pedido</td></tr>";
            }
            $("#itensPedidoTable").html(tbodyi);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert("Erro ao carregar itens do pedido");
            for (i in XMLHttpRequest) {
                if (i != "channel") console.log(i + " : " + XMLHttpRequest[i]);
            }
        },
    });
}

// Open modal on button click
$(document).on("click", ".btnAddItemPedido", function () {
    var pedidoId = $(this).closest("tr").find("td:first").text().trim();

    $("#itensPedidoForm")[0].reset();
    $("#itemPedidoId").val(0);
    $("#itemPedidoPedidoId").val(pedidoId);

    // Load products into product select
    $.ajax({
        url: "produtos-list",
        method: "GET",
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            var productSelect = $("#product_id");
            productSelect.empty();
            productSelect.append(
                '<option value="">Selecione um produto</option>'
            );
            data.forEach(function (produto) {
                productSelect.append(
                    '<option value="' +
                        produto.id +
                        '">' +
                        produto.name +
                        "</option>"
                );
            });
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert("Erro ao carregar itens do pedido");
            for (i in XMLHttpRequest) {
                if (i != "channel") console.log(i + " : " + XMLHttpRequest[i]);
            }
        },
    });

    //carregarMarcasProduto();
    loadPedidoItems(pedidoId);
    itemsPedidoModal.show();
});

// Submit item form
$("#itensPedidoForm").submit(function (e) {
    e.preventDefault();
    var valor_unitario = $("#valor_unitario")
        .val()
        .replace(/\./g, "")
        .replace(",", ".");
    var quantidade = parseFloat($("#quantidade").val());
    var valor_total = (quantidade * valor_unitario).toFixed(2);

    $("#loadingItem").show();
    var formData = {
        id: $("#itemPedidoId").val(),
        pedido_id: $("#itemPedidoPedidoId").val(),
        product_id: $("#product_id").val(),
        marca_id: $("#marca_id").val(),
        quantidade: $("#quantidade").val(),
        unidade: $("#unidade").val(),
        valor_unitario: valor_unitario,
        valor_total: valor_total,
        _token: $('meta[name="csrf-token"]').attr("content"),
    };

    $.ajax({
        url: "salvar-item-pedido",
        method: "POST",
        data: formData,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            $("#loadingItem").hide();
            alert("Item salvo com sucesso");
            loadPedidoItems(formData.pedido_id);
            $("#itensPedidoForm")[0].reset();
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert("Erro ao salvar item");
            for (i in XMLHttpRequest) {
                if (i != "channel") console.log(i + " : " + XMLHttpRequest[i]);
            }
        },
    });
});

// Edit item button click
$(document).on("click", ".editItemBtn", function () {
    var itemId = $(this).data("id");

    $.ajax({
        url: "itens-pedido-get",
        method: "POST",
        data: { id: itemId },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            //console.log(data);
            $("#itemPedidoId").val(data.id);
            $("#itemPedidoPedidoId").val(data.pedido_id);
            $("#product_id").val(data.product_id);
            carregarMarcasProduto(data.product_id);
            //$("#marca_id").val(data.marca_id);
            setTimeout("popularMarcaa(" + data.marca_id + ")", 500);
            $("#quantidade").val(data.quantidade);
            // Removed peso field usage
            // $("#peso").val(data.peso);
            $("#unidade").val(data.unidade);
            $("#valor_item").val(number_format(data.valor, 2, ",", "."));
            itemsPedidoModal.show();
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert("Erro ao carregar item");
            for (i in XMLHttpRequest) {
                if (i != "channel") console.log(i + " : " + XMLHttpRequest[i]);
            }
        },
    });
});

function popularMarcaa(id) {
    $("#marca_id").val(id);
}

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
