app.controller("Enquete_configuracao", function($scope, $http)
{

    $scope.load_grid = function ()
    {
        var model = "Enquete_configuracao";
        var id = "enquete_configuracao_id";

        $("#grid").kendoGrid({
            dataSource: {
                type: "jsonp",
                transport: {
                    read: base_url + 'services/Requisicao/get_all/' + model
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
            filterable: false,
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
                    field: "enquete_nome",
                    title: "Nome da enquete",
                    width:150,
                },
                {
                    title: "",
                    template: kendo_get_actions(model, id),
                    width: 100
                },
            ],
            dataBound : function()
            {
                $scope.$apply();
            }
        });


    }


});