$(document).ready(function(){

    $(".inputmask-porcento").inputmask('99,99'); //, { numericInput: true, rightAlignNumerics: false, greedy: true});
    $(".inputmask-numero").inputmask('integer'); //, { numericInput: true, rightAlignNumerics: false, greedy: true});
    $(".inputmask-date").inputmask("d/m/y",{ "placeholder": "__/__/____" });

    calculo_preco();

    $('.repasse_comissao').on('blur',function() {

        $('.repasse_comissao').val($(this).val());

        calculo_preco();
    });

    $('.desconto_condicional').on('blur',function() {

        console.log('desconto_condicional: ', $(this).val());
        var desconto = $(this).val();
        $('.desconto_condicional').val(desconto);
        $('.desconto_condicional').val(desconto);
        calculo_preco();

    });

    $('#btn_recalcular').on('click',function() {

        console.log('botao recalcular');
        calculo_preco();

    });

    $('.quantidade').on('blur',function() {

        console.log('num quantidade: ', $(this).val());

        var quantidade = parseInt($(this).val());

        if(quantidade > 99) {
            quantidade = 99;
        }else if(quantidade < 1){
            quantidade = 1;
        }
        $('.quantidade').val(quantidade);
        calculo_preco();

    });

});


/**
 * Calculo do preço da cotação
 */
function calculo_preco()
{


    toastr.clear();

    //Dados para post
    var data =
    {
        produto_parceiro_id: $('#produto_parceiro_id').val(),
        parceiro_id: $('#parceiro_id').val(), //parceiro logado
        quantidade: $('.quantidade').val(),
        equipamento_id: $('#equipamento_id').val(),
        repasse_comissao: $('.repasse_comissao').val(),
        desconto_condicional: $('.desconto_condicional').val(),
        cotacao_id: $('#cotacao_id').val(),
        'coberturas' : []
    }

    $('.ck-cobertura-adicional:checked').each(function() {
        data['coberturas'].push($(this).val());
    });

    var url = $('#url_calculo').val();
    console.log('calculo:', data);

    /**
     * Efetua post para retornar cálculo de cotação
     */
    $.ajax({
            type: "POST",
            url: url,
            cache: false,
            data: data,
        })
        .done(function( result )
        {
            console.log('result', result);
            // debugger;

            //Se sucesso
            if(result.status == true)
            {
                //Seta diferença dos dias
                $('.comissao_corretor').html(numeroParaMoeda(result.comissao, 2, ',', ''));
                $('.desconto_upgrade').html('-' + numeroParaMoeda(result.desconto_upgrade, 2, ',', ''));
                $('.repasse_comissao').val(result.repasse_comissao);
                $('.desconto_condicional_valor').val(result.desconto_condicional_valor);

                $.each(result.valores_bruto, function (idx, obj)
                {
                    $('.premio_bruto_one_'+idx).html(numeroParaMoeda(parseFloat(obj).toFixed(3), 2, ',', '.'));
                    $('.premio_bruto_two_'+idx).html(numeroParaMoeda(parseFloat(obj).toFixed(3), 2, ',', '.'));

                    $('.valor_cobertura_adicional_one_'+idx).html('00,00');
                    $('.valor_cobertura_adicional_two_'+idx).html('00,00');

                });

                $.each(result.valores_liquido, function (idx, obj)
                {
                    $('.premio_liquido_one_'+idx).html(numeroParaMoeda(parseFloat(obj).toFixed(3), 2, ',', '.'));
                    $('.premio_liquido_two_'+idx).html(numeroParaMoeda(parseFloat(obj).toFixed(3), 2, ',', '.'));
                });

                var tudo_igual = true;

                $.each(result.valores_liquido_total, function (idx, obj)
                {
                    //Verifica se bruto e líquido é igual
                    if(obj != result.valores_bruto[idx])
                    {
                        tudo_igual = false;
                    }

                    $('.premio_total_one_'+idx).html(numeroParaMoeda(parseFloat(obj).toFixed(3), 2, ',', '.'));
                    $('.premio_total_two_'+idx).html(numeroParaMoeda(parseFloat(obj).toFixed(3), 2, ',', '.'));
                });

                console.log('coberturas; ', result.valores_cobertura_adicional);

                $.each(result.valores_totais_cobertura_adicional, function (idx, obj)
                {
                    $('.valor_cobertura_adicional_one_'+idx).html(numeroParaMoeda(parseFloat(obj).toFixed(3), 2, ',', '.'));
                    $('.valor_cobertura_adicional_two_'+idx).html(numeroParaMoeda(parseFloat(obj).toFixed(3), 2, ',', '.'));
                });

                $.each(result.valores_cobertura_adicional, function (idx, obj)
                {

                    $('#cobertura_adicional_valores_one_'+idx).val(obj.join(';'));
                    $('#cobertura_adicional_valores_two_'+idx).val(obj.join(';'));
                });


                //Se o valor bruto e líquido for igual, esconde os campos
                if(tudo_igual)
                {
                    /*
                    $(".premio_liquido").closest("tr").hide();
                    $(".premio_bruto").closest("tr").hide();
                    $(".li_premio_liquido").hide();
                    $(".li_premio_liquido_total").hide();*/

                    $(".premio_liquido").closest("tr").hide();
                    // $(".premio_bruto").closest("tr").hide();
                    //
                    $(".li_premio_liquido").hide();
                }


                toastr.success("Calculo efetuado com sucesso!", "Calcular cotação");
                $('.td-add-car').show();
            }else{
                toastr.error(result.mensagem, "Atenção!");

                $('.td-add-car').hide();
                $('#' + result.campo).focus();


            }
        }
        // .error(function(x){
        //     debugger;
        // })
        );
}


