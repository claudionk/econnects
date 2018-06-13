/**
 * Created by Leonardo Lazarini on 21/09/2016.
 */

$(function() {

    var data = [];
    var pgridw;

    inicializaOrb();

    /**
     * Filtrar
     */
    $("#btnFiltro").click(function()
    {
        var post = {
            'data_inicio' : $("#data_inicio").val(),
            'data_fim' : $("#data_fim").val()
        };

        $.ajax({
            url: base_url + 'admin/relatorios/getRelatorio',
            method: "get",
            dataType: 'json',
            data: post
        })
            .done(function(result) {
                if(result.status)
                {
                    data = result.data;
                    parseData(data);

                    if(data.length <= 0)
                    {
                        toastr.error("Nenhum dado retornado para este filtro.", "Mude o filtro");
                        $("#data_inicio").focus();
                        $(".orb").css("opacity", "0.3")
                    }
                    else
                    {
                        toastr.success("Filtragem realizada com sucesso. " + data.length + " vendas retornadas.", "Sucesso!");
                        $(".orb").css("opacity", "1")
                    }

                    pgridw.refreshData(data);
                }
            });
    })

    /**
     * Parcionar data
     * @param data
     */
    function parseData(data)
    {
        $.each(data, function(i, val)
        {
            data[i].quantia = 1;
            data[i].valor_total = parseNumero(val.valor_total);
            data[i].valor_parcela = parseNumero(val.valor_parcela);
            data[i].premio_liquido = parseNumero(val.premio_liquido);
            data[i].comissao_corretor = parseNumero(val.comissao_corretor);
            data[i].premio_liquido_total = parseNumero(val.premio_liquido_total);
        })
    }

    /**
     * Inicializa ORB
     */
    function inicializaOrb()
    {
        parseData(data);

        var config =
        {
            dataHeadersLocation: 'columns',
            theme: 'gray',
            toolbar: {
                visible: true
            },
            grandTotal: {
                rowsvisible: true,
                columnsvisible: true
            },
            subTotal: {
                visible: true,
                collapsed: true
            },
            dataSource: data,
            fields:
            [
                { name: 'status', caption: 'Status' },
                { name: 'nome_produto_parceiro', caption: 'ProdutoParceiro' },
                { name: 'nome_produto', caption: 'Produto' },
                { name: 'nome_fantasia', caption: 'Parceiro' },
                { name: 'plano_nome', caption: 'Plano'},
                { name: 'codigo', caption: 'Código' },
                { name: 'quantia', caption: 'QuantidadeVendas', aggregateFunc: "count" },
                { name: 'valor_parcela', caption: 'ValorParcelas' },
                { name: 'premio_liquido', caption: 'PremioBruto' },
                { name: 'premio_liquido_total', caption: 'PremioLiquido' },
                { name: 'comissao_corretor', caption: 'ComissãoCorretor' },
            ],

            rows    : [ 'Parceiro' ],
            columns : [ 'ProdutoParceiro', 'Plano' ],
            data    : [ 'PremioBruto', 'PremioLiquido', 'QuantidadeVendas'],
        };

        pgridw = new orb.pgridwidget(config);;
        pgridw.render(document.getElementById('relatorio_container'));


        $(".orb").css("opacity", "0.9")
    }


})