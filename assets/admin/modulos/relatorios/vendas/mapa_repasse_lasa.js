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

                { name: 'operacao', caption: 'Operacao' },
                { name: 'grupo', caption: 'Grupo' },
                { name: 'data_emissao', caption: 'DataVenda' },
                { name: 'data_cancelamento', caption: 'DataCancelamento' },
                { name: 'ini_vigencia', caption: 'InicioVigencia' },
                { name: 'fim_vigencia', caption: 'FimVigencia' },
                { name: 'documento', caption: 'Documento' },
                { name: 'num_apolice', caption: 'NumBilhete' },
                { name: 'num_endosso', caption: 'NumEndosso' },
                { name: 'segurado_nome', caption: 'Segurado' },
                { name: 'equipamento', caption: 'Equipamento' },
                { name: 'marca', caption: 'Marca' },
                { name: 'modelo', caption: 'Modelo' },
                { name: 'imei', caption: 'IMEI' },
                { name: 'importancia_segurada', caption: 'ImportanciaSegurada' },
                { name: 'vigencia_parcela', caption: 'VigenciaParcela' },
                { name: 'parcela', caption: 'Parcela' },
                { name: 'status_parcela', caption: 'StatusParcela' },
                { name: 'valor_parcela', caption: 'ValorParcela' },
                { name: 'cobertura', caption: 'Cobertura' },
                { name: 'PB_cob', caption: 'PremioBrutoCobertura' },
                { name: 'iof_cob', caption: 'IOF' },
                { name: 'PL_cob', caption: 'PremioLiquidoCobertura' },
                { name: 'cd_tipo_comissao', caption: 'TipoComissao' },
                { name: 'valor_comissao', caption: 'ValorComissao' },

            ],

            rows    : [ 'Parceiro' ],
            // columns : [ 'ProdutoParceiro', 'Plano' ],
            columns : [ 'Operacao', 'Grupo', 'DataVenda', 'DataCancelamento', 'InicioVigencia', 'FimVigencia', 'Documento', 'NumBilhete', 'NumEndosso', 'Segurado', 'Equipamento', 'Marca', 'Modelo', 'IMEI', 'ImportanciaSegurada', 'VigenciaParcela', 'Parcela', 'StatusParcela', 'ValorParcela', 'Cobertura', 'PremioBrutoCobertura', 'IOF', 'PremioLiquidoCobertura', 'TipoComissao'  ],
            
            data    : [ 'PremioBruto', 'PremioLiquido', 'QuantidadeVendas'],
            // data    : [ 'Operacao', 'Grupo', 'DataVenda', 'DataCancelamento', 'InicioVigencia', 'FimVigencia', 'Documento', 'NumBilhete', 'NumEndosso', 'Segurado', 'Equipamento', 'Marca', 'Modelo', 'IMEI', 'ImportanciaSegurada', 'VigenciaParcela', 'Parcela', 'StatusParcela', 'ValorParcela', 'Cobertura', 'PremioBrutoCobertura', 'IOF', 'PremioLiquidoCobertura', 'TipoComissao' ],
        };

        pgridw = new orb.pgridwidget(config);
        pgridw.render(document.getElementById('relatorio_container'));

        $(".orb").css("opacity", "0.9")
    }

})