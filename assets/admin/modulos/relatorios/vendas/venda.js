/**
 * Created by Leonardo Lazarini on 21/09/2016.
 */

$(function() {

    inicializaOrb();

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

        pgridw = new orb.pgridwidget(config);
        pgridw.render(document.getElementById('relatorio_container'));

        $(".orb").css("opacity", "0.9")
    }

})