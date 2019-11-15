app.controller("Enquete", function($scope, $http)
{

    $scope.init = function ()
    {
        var model = "Enquete_resposta";
        var id = "enquete_resposta_id";

        var filter = {
            filter: {
                filters: [
                    { field:'enquete_id', operator: '=', value: enquete_id },
                ]
            }
        }

        var template_btns = '';
        template_btns = '<a class="btn ink-reaction btn-raised btn-xs btn-primary" href="'+base_url+'admin/Enquete/detalhes/#= ' + id + '#"><i class="fa fa-eye"></i> Detalhes</a>';


        $("#grid_respostas").kendoGrid({
            dataSource: {
                type: "jsonp",
                transport: {
                    read: base_url + 'services/Requisicao/get_all/' + model + "?" + $.param(filter)
                },
                schema: {
                    data: 'data',
                    total: 'total'
                },
                pageSize: 10,
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true,
            },
            height: 550,
            sortable: true,
            filterable: true,
            pageable: {
                refresh: true,
                pageSizes: true,
                buttonCount: 5
            },
            columns: [
                {
                    field: id,
                    title: "#ID",
                    width: 50,
                },
                {
                    field: "id_exp",
                    title: "OS",
                    width:50,
                },
                {
                    field: "respondido",
                    title: "Respondido",
                    width:50,
                },
                {
                    field: "data_enviada",
                    title: "Data Enviada",
                    width:50,

                },
                {
                    field: 'data_respondido',
                    title: "Data Respondido",
                    width:50,
                },
                {
                    field: '',
                    template: template_btns,
                    width:40,
                },

            ],
            dataBound : function()
            {
                $scope.$apply();

            }
        });

        $scope.monta_perguntas();
    }

    $scope.monta_perguntas = function(){


        $scope.perguntas = perguntas;


        setTimeout(function()
        {
            var count = 0;

            $.each(perguntas, function(i, val) {

                var series = [];

                val.forEach(function(resposta, x){
                    series.push({
                        category: resposta.resposta,
                        value: parseInt(resposta.quantidade),
                    })
                })

                $scope.perguntas[i].pergunta = val[0].pergunta;

                console.log($scope.perguntas[i]);

                console.log("Iniciando #pergunta_" + i + " .grafico");
                $("#pergunta_" + count + " .grafico").kendoChart({
                    legend: {
                        visible: true
                    },
                    seriesDefaults: {

                        labels: {
                            visible: true,
                            background: "transparent",
                            template: "#= kendo.format('{0:P}', percentage)#",
                        }

                    },
                    series: [{
                        type: "pie",
                        startAngle: 150,
                        data: series,
                    }],
                    tooltip: {
                        visible: true,
                        format: "{0} respostas",
                    }
                });

                count++;
            })
        }, 2000)


    }


    $scope.init();
});