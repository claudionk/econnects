$(document).ready(function(){

    show_hide_campos();

    $('input[name=repasse_comissao]').change(function () {
        show_hide_campos();
    });

    $('input[name=desconto_habilitado]').change(function () {
        show_hide_campos();
    });

    $('input[name=comissao_tipo]').change(function () { 
        show_hide_campos(); 
    });

    $("#pai_id").chained("#produto_parceiro_id");

});

function show_hide_campos(){

    if(($('input[name=repasse_comissao]:checked').val() == '1')){
        $(".repasse_habilitado").show();
    }else {
        $(".repasse_habilitado").hide();
    }

    if(($('input[name=desconto_habilitado]:checked').val() == '1')){
        $(".desconto_habilitado").show();
    }else {
        $(".desconto_habilitado").hide();
    }

    if($('input[name=comissao_tipo]:checked').val() == '0'){ 
        $(".comissao_tipo").show(); 
    }else{ 
        $(".comissao_tipo").hide(); 
    } 

}

