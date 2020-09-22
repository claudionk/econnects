
    $(window).load(function(){

        $('#modal-termo_aceite').modal();

    });


    $('#modal-termo_aceite').on('hidden.bs.modal', function () {
        document.location.reload();
    })

    $("#aceitar_termo").on("submit", function(e){
        e.preventDefault();
        var $self = $(this);
        var $aceitarTermoError = $("#aceitar_termo-error");        
        var confirmarSenha = $self.find('[name="confirmar_senha"]').val();

        var data = {};
        data.ajax       = 1;
        data.senha_atual = $self.find('[name="senha_atual"]').val();
        data.senha_nova  = $self.find('[name="senha_nova"]').val();

        if(data.senha_nova != confirmarSenha){
            $aceitarTermoError.html("Senhas incompativeis");
            return false;
        }

        var url = this.action;
        $.ajax({
            url: url,
            type: "POST",
            dataType: "json",
            data: data,
            success: function(oResponse){
                if(oResponse.status == true){
                    location.reload();
                }else{
                    $aceitarTermoError.html(oResponse.message);
                }
            },
            error: function(oResponse){
                $aceitarTermoError.html(oResponse.responseText);
            }
        });
    });

