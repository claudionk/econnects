app.controller("Enquete", function($scope, $http)
{

    $scope.load_grid = function ()
    {
        var model = "Enquete";
        var id = "enquete_id";


        var template_btns = '';
        template_btns = '<a class="btn ink-reaction btn-raised btn-xs btn-primary" href="'+base_url+'admin/'+model+'/edit/#= ' + id + '#"><i class="fa fa-edit"></i> Editar</a>';
        template_btns += '<a class="btn ink-reaction btn-raised btn-xs btn-default" target="_blank" href="'+base_url+'front/'+model+'/responder/ver/#= ' + id + '#"><i class="fa fa-eye"></i> Visualizar</a>';
        template_btns += '<a class="btn ink-reaction btn-raised btn-xs btn-default" target="_blank" href="'+base_url+'admin/'+model+'/dashboard/#= ' + id + '#"><i class="fa fa-dashboard"></i> Dashboard</a>';
        template_btns += '<a class="btn ink-reaction btn-raised btn-xs btn-danger" href="'+base_url+'admin/'+model+'/delete/#= ' + id + '#"><i class="fa fa-remove"></i> Deletar</a>';


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
                    field: "nome",
                    title: "Nome da enquete",
                    width:100,
                },

                {
                    title: "",
                    template: template_btns,
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