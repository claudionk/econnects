$(function(){

    $.ajax({
        url: base_url + "admin/comunicacao_controller/getComunicacao",
        method: 'get',
        dataType: 'json'

    }).done(function(resposta)
    {
        if(resposta.status)
        {
            new Morris.Line({
                element: 'comunicacao_por_dia',
                data: resposta.data.comunicacao_por_dia,
                xkey: 'dia',
                ykeys: ['enviados'],
                labels: ['Enviados']
            });



            new Morris.Bar({
                element: 'comunicacao_por_engine',
                data: resposta.data.comunicacao_por_engine,
                xkey: 'nome',
                ykeys: ['total_enviados'],
                labels: ['Total comunicação enviados por engine']
            });

            new Morris.Bar({
                element: 'comunicacao_por_parceiro',
                data: resposta.data.comunicacao_por_parceiro,
                xkey: 'nome',
                ykeys: ['total_enviados'],
                labels: ['Total comunicação enviados por parceiro']
            });
        }

    });



});

function createChart(dados)
{

}


