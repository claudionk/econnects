app.controller("Enquete", function($scope, $http)
{
    $scope.perguntas = [];

    $scope.init = function(){

        $scope.ms_gatilhos =  $("#gatilhos").kendoMultiSelect()
            .data("kendoMultiSelect");

        $scope.ms_clientes =  $("#clientes").kendoMultiSelect({
            change: function()
            {
                var filters = buildFilters(this.dataItems());
                $scope.ms_estipulantes.dataSource.filter(filters);
                $scope.ms_contratos.dataSource.filter(filters);
            }
        }).data("kendoMultiSelect");

        var filters = {
            sort: [
                { field: 'estipulante', dir: 'asc'}
            ]
        }

        $scope.ms_estipulantes = $("#estipulantes").kendoMultiSelect({
            placeholder: "Selecione os estipulantes...",
            dataTextField: "estipulante",
            dataValueField: "cod_estipulante",
            autoBind: false,
            dataSource: {
                type: "jsonp",
                transport: {
                    read: base_url + 'services/Requisicao/get_all/Sis_clientes_estipulantes?' + $.param(filters)
                },
                schema: {
                    data: 'data',
                    total: 'total'
                },
                pageSize: 1000,
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true,
            },
            value: estipulantes_valores,
        }).data("kendoMultiSelect");


        $scope.ms_prestacoes = $("#prestacoes").kendoMultiSelect()
            .data("kendoMultiSelect");


        var filters = {
            sort: [
                { field: 'descricao', dir: 'asc'}
            ]
        }
        $scope.ms_contratos = $("#contratos").kendoMultiSelect({
            placeholder: "Selecione os estipulantes...",
            dataTextField: "descricao",
            dataValueField: "id_contrato",
            autoBind: false,
            dataSource: {
                type: "jsonp",
                transport: {
                    read: base_url + 'services/Requisicao/get_all/Sis_contratos?' + $.param(filters)
                },
                schema: {
                    data: 'data',
                    total: 'total'
                },
                pageSize: 1000,
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true,
            },
            value: contratos_valores,
        }).data("kendoMultiSelect");

        $scope.ms_clientes.trigger("change");

        /*

        $scope.ms_contratos = $("#contratos").kendoMultiSelect()
            .data("kendoMultiSelect");
            */
    }

    $scope.adicionar_pergunta = function()
    {
        $scope.perguntas.push({});
        toastr.success("A pergunta foi adicionada ao final da lista com sucesso.");
    }

    $scope.remover_pergunta = function(index)
    {
        $scope.perguntas.splice(index, 1);
        toastr.success("A pergunta foi removida com sucesso.");
    }

    $scope.init();
})

function buildFilters(dataItems) {
    var filters = [],
        length = dataItems.length,
        idx = 0, dataItem;

    for (; idx < length; idx++) {

        console.log(dataItem);
        dataItem = dataItems[idx];

        filters.push({
            field: "id_cliente",
            operator: "eq",
            value: parseInt(dataItem.value)
        });
    }

    return {
        logic: "or",
        filters: filters
    };
}