$(function(){
    $("#cobertura_id").chained("#cobertura_tipo_id");
    mostrar_exibir();
    $("#mostrar").change(function() {
        mostrar_exibir();
    });
});

function mostrar_exibir() {
    switch( $('#mostrar').val() ){
        case "descricao":
            $('.descricao').show();
            $('.preco').hide();
            $('.porcentagem').show();
        break;
        case "preco":
            $('.descricao').hide();
            $('.preco').show();
            $('.porcentagem').hide();
        break;
        case "importancia_segurada":
            $('.descricao').hide();
            $('.preco').hide();
            $('.porcentagem').show();
        break;
    }
}
