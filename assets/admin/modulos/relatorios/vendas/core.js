/**
 * Created by Leonardo Lazarini on 21/09/2016.
 */
var data = [];
var pgridw;

/**
 * Parcionar data
 * @param data
 */
function parseData(data)
{
    jQuery.each(data, function(i, val)
    {
        data[i].quantia = 1;
        data[i].status_data = val.status_data;
        data[i].cliente = val.cliente;
        data[i].documento = val.documento;
        data[i].nome_produto_parceiro = val.nome_produto_parceiro;
        data[i].num_apolice = val.num_apolice;
        data[i].plano_nome = val.plano_nome;
        data[i].nota_fiscal_valor = parseNumero(val.nota_fiscal_valor);
        data[i].valor_total = parseNumero(val.valor_total);
        data[i].valor_parcela = parseNumero(val.valor_parcela);
        data[i].premio_liquido = parseNumero(val.premio_liquido);
        data[i].premio_liquido_total = parseNumero(val.premio_liquido_total);
        data[i].comissao_corretor = parseNumero(val.comissao_corretor);
    })
}
 
$(function() {
    /**
     * Filtrar
     */
    $("#btnFiltro").click(function()
    {
        var post = {
            'data_inicio' : $("#data_inicio").val(),
            'data_fim' : $("#data_fim").val()
        };

        var action = ($('#action').length == 0 || $('#action').val() == '') ? 'getRelatorio' : $('#action').val();
        $.ajax({
            url: base_url + 'admin/relatorios/'+ action,
            method: "post",
            dataType: 'json',
            data: post,
            beforeSend: function () {
                // Carregando...
                $.blockUI({ message: null });
                $("#processando").show();
            }
        })
        .done(function(result) {
            $.unblockUI();
            $("#processando").hide();

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
    });


})