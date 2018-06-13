
$(document).ready(function(){

    $("#forma_pagamento_id").change(function () {

        mostrar_exibir();
    });


    mostrar_exibir();

});


function mostrar_exibir(){

    if($("#forma_pagamento_id").val() == '5'){
        $('.boleto').show(200);
    }else{
        $('.boleto').hide(200);
    }

}



