
$(document).ready(function(){


    show_hide_campos();


    $('#comissao_tipo_id').change(function () {

        show_hide_campos();
    });

});


function show_hide_campos(){


    if($('#comissao_tipo_id').val() == '1'){
        $(".comissao").show();
    }else{
        $(".comissao").hide();
    }

}




