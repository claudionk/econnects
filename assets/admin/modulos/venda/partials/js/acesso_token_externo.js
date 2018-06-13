$(function(){

    var produto_parceiro_id = $("#produto_parceiro_id").val();

    $("#bt_enviar_email").click(function(){

        dispara_evento_url_pagamento("email");

    })

    $("#bt_enviar_sms").click(function(){
        dispara_evento_url_pagamento("sms");

    })

    function dispara_evento_url_pagamento (tipo)
    {
        $.ajax(base_url + "admin/venda/envia_comunicacao_url_pagamento/", {
            data: {
                produto_parceiro_id : produto_parceiro_id,
                contato: $("#" + tipo).val(),
                url_acesso_externo: $("#url_acesso_externo").val(),
                nome: $("#nome_contato").val(),
                tipo: tipo,
            },
            dataType: 'json',
            type:'POST',
            success: function(data)
            {
                if(data.status)
                {
                    toastr.success("Sucesso ao enviar a comunicação.")
                    $("#modal_acesso_externo").modal("hide");
                }
                else
                {
                    toastr.error(data.mensagem)
                }
            }
        })
    }
})