
$(document).ready(function(){


    show_hide_campos();


    $('input[name=habilitado]').change(function () {

        show_hide_campos();
    });

});


function show_hide_campos(){



    if(($('input[name=habilitado]:checked').val() == '1')){
        $(".desconto_habilitado").show();
    }else {
        $(".desconto_habilitado").hide();
    }


}

//


