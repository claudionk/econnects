
$(document).ready(function(){


    show_hide_campos();


    $('input[name=calculo_tipo]').change(function () {

        show_hide_campos();
    });

    $('input[name=inad_hab]').change(function () {

        show_hide_campos();
    });

    $('input[name=inad_reativacao_hab]').change(function () {

        show_hide_campos();
    });

    $('input[name=seg_antes_hab]').change(function () {

        show_hide_campos();
    });


});


function show_hide_campos(){



    if(($('input[name=inad_hab]:checked').val() == '1')){
        $(".inadimplencia_habilitado").show();
    }else {
        $(".inadimplencia_habilitado").hide();
    }


    if(($('input[name=inad_reativacao_hab]:checked').val() == '1')){
        $(".inadimplencia_reativacao").show();
    }else {
        $(".inadimplencia_reativacao").hide();
    }

    if(($('input[name=seg_antes_hab]:checked').val() == '1')){
        $(".antes_habilitado").show();
    }else {
        $(".antes_habilitado").hide();
    }

    if(($('input[name=calculo_tipo]:checked').val() == 'E')){
        $(".cancelamento_especial").show();
    }else {
        $(".cancelamento_especial").hide();
    }

}

//


