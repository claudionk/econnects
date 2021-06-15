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

function refresh_comissao(){
    var url = window.location.href;
    var url_edit = '';
    var url_param = url.split('/');
    for(var i = 0, len = url_param.length; i < len; ++i) {
        if (url_param[i] == 'edit'){
            url_edit = '/' + url_param[i] + '/' + url_param[i+1];
            if (url_param[i+2] == undefined){
                url += '/' + $('select[name=parceiro_relacionamento_produto_vigencia_id]').val();
                break;
            }else{
                url_param[i+2] = $('select[name=parceiro_relacionamento_produto_vigencia_id]').val();
                url = url.replace(url_edit,url_edit + '/' + $('select[name=parceiro_relacionamento_produto_vigencia_id]').val());
                break;
            }
        }
    }
    window.location.href = url;
}
