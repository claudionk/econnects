
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
    $(document).ready( function(){ $("#unidade_tempo").change() })