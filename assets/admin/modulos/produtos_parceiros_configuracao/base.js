
$(document).ready(function(){

    show_hide_campos();

    $('input[name=pagamento_tipo]').change(function () {

        show_hide_campos();
    });

    $('input[name=repasse_comissao]').change(function () {
        show_hide_campos();
    });

    $('input[name=padrao_repasse_comissao]').change(function () {
        show_hide_campos();
    });

    $('input[name=pagmaneto_cobranca]').change(function () {
        console.log($('input[name=pagmaneto_cobranca]:checked').val());
        show_hide_campos();
    });

    $("input[name='venda_habilitada_web']").change(function(){
        show_hide_campos();
    })



});


function show_hide_campos(){


    if($('input[name=pagamento_tipo]:checked').val() == 'UNICO'){
        $(".tipo-pagamento-filho").hide();
    }else{
        $(".tipo-pagamento-filho").show();
        if($('input[name=pagmaneto_cobranca]:checked').val() == 'VENCIMENTO_CARTAO'){
            $(".tipo-pagamento-filho-two").show();
        }else{
            $(".tipo-pagamento-filho-two").hide();
        }
    }

    if($('input[name=repasse_comissao]:checked').val() == '0'){
        $(".repasse_maximo").hide();
    }else{
       $(".repasse_maximo").show();
    }

    if($('input[name=padrao_repasse_comissao]:checked').val() == '0'){
        $(".padrao_repasse_maximo").hide();
    }else{
       $(".padrao_repasse_maximo").show();
    }

    if($("input[name='venda_habilitada_web']:checked").val() == '0')
    {
        $("#url_venda_online").hide();
    }
    else
    {
        $("#url_venda_online").show();
    }
}




