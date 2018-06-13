var last = 0;
function buscaCidades(idEstado, base_url, idCidade, nomeCidade)
{
    var setaCidade = false;

    if(idEstado != last)
    {
        $.post(base_url+"admin/localidade_cidades/buscaCidadesPorEstado", 
        {
            idEstado : idEstado
        }, 
        function(data)
        {

            var obj = jQuery.parseJSON(data);
            var $select = $('#localidade_cidade_id');
            $('#localidade_cidade_id').html(data);
            if(!obj.status)
                alert(obj.message);
            else
            {
                for (i = 0; i < Object.keys(obj.rows).length; ++i) 
                {
                    var $option = $("<option/>").attr("value", obj.rows[i].localidade_cidade_id)
                                  .text(obj.rows[i].nome);
                    if(idCidade != 0)
                    {
                        if(obj.rows[i].localidade_cidade_id == idCidade)
                        {
                            $option = $option.attr('selected', 'selected');
                        }
                    } 
                    else if(nomeCidade != null)
                    {
                        if(obj.rows[i].nome == nomeCidade)
                        {
                            $option = $option.attr('selected', 'selected');
                        }
                    }
                    $select.append($option);
                }
            }
        });
    last = idEstado;
    return true;
    }
    return false;
}