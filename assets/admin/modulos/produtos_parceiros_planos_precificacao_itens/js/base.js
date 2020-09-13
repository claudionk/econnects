function format_field( field , test_int){
    if( test_int ){
        console.log(  $(field).val() )
        $(field).val( String( $(field).val() ).replace(/(\.|,).+$/,"") );   
    }
    else{
        $(field).val( String( $(field).val() ).replace(/\./,",") );
    }
}
$("#unidade_tempo").bind("change", function(){ 
    format_field( "#inicial" , $("option:selected","#unidade_tempo").val() != "VALOR" )
    format_field( "#final" , $("option:selected","#unidade_tempo").val() != "VALOR" )
    format_field( "#valor" , false );
})
$("#inicial").bind("blur", function(){ 
    format_field( "#inicial" , $("option:selected","#unidade_tempo").val() != "VALOR" );
})
$("#final").bind("blur", function(){ 
    format_field( "#final" , $("option:selected","#unidade_tempo").val() != "VALOR" );
})
$("#valor").bind("blur", function(){ 
    format_field( "#valor" , false );
})
$(document).ready( function(){ 
    $("#unidade_tempo").change();

    $('#addRule').bind("click", function(){
        var $clone = $('.lineDuplicate:last').clone(true, true).insertAfter(".lineDuplicate:last");

        $clone.find('.unidade_tempo').attr('name', 'unidade_tempo_[]').val('');
        $clone.find('.inicial').attr('name', 'inicial_[]').val('');
        $clone.find('.final').attr('name', 'final_[]').val('');
    });
})