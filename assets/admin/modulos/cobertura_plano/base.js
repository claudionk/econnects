$(function(){

    $("#cobertura_id").chained("#cobertura_tipo_id");




    mostrar_exibir();
    $("#mostrar").change(function() {
        mostrar_exibir();
    });

});

function mostrar_exibir(){
    if($('#mostrar').val() == 'descricao'){
        $('.descricao').show();
        $('.preco').hide();
        $('.porcentagem').hide();
    }else{
        $('.descricao').hide();
        $('.preco').show();
        $('.porcentagem').show();
    }
}




