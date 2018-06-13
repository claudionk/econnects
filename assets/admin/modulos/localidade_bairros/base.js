var last = 0;
function buscaBairros(id_cidade, base_url, id_bairro, nomeBairro)
{
    var setaCidade = false;

    if(id_bairro != last)
    {
        $.post(base_url+"admin/localidade_bairros/buscaBairrosPorCidades",
        {
            id_cidade : id_cidade
        }, 
        function(data)
        {

            var obj = jQuery.parseJSON(data);
            var $select = $('#localidade_bairro_id');
            $('#localidade_bairro_id').html(data);
            if(!obj.status)
                alert(obj.message);
            else
            {
                for (i = 0; i < Object.keys(obj.rows).length; ++i) 
                {
                    var $option = $("<option/>").attr("value", obj.rows[i].localidade_bairro_id)
                                  .text(obj.rows[i].nome);
                    if(id_bairro != 0)
                    {
                        if(obj.rows[i].localidade_bairro_id == id_bairro)
                        {
                            $option = $option.attr('selected', 'selected');
                        }
                    } 
                    else if(nomeBairro != null)
                    {
                        if(obj.rows[i].nome == nomeBairro)
                        {
                            $option = $option.attr('selected', 'selected');
                        }
                    }
                    $select.append($option);
                }
            }
        });
    last = id_bairro;
    return true;
    }
    return false;
}

$(function(){

    $("#localidade_estado_id").change(function() {
        buscaCidades($(this).val(), base_url, 0, null)
    });


    if($('#cidade_id').val()){
        console.log($('#cidade_id').val());
        buscaCidades($('#localidade_estado_id').val(), base_url, $('#cidade_id').val(), null);
    }else{
        buscaCidades($('#localidade_estado_id').val(), base_url, 0, null);
    }
    


});