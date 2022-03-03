function refresh_cobranca(){
    var url = window.location.href;
    var url_edit = '';
    var url_param = url.split('/');
    for(var i = 0, len = url_param.length; i < len; ++i) {
        if (url_param[i] == 'edit'){
            url_edit = '/' + url_param[i] + '/' + url_param[i+1];
            if (url_param[i+2] == undefined){
                url += '/' + $('select[name=produto_parceiro_autorizacao_cobranca_id]').val();
                break;
            }else{
                url_param[i+2] = $('select[name=produto_parceiro_autorizacao_cobranca_id]').val();
                url = url.replace(url_edit,url_edit + '/' + $('select[name=produto_parceiro_autorizacao_cobranca_id]').val());
                break;
            }
        }
    }
    window.location.href = url;
}
