$(document).ready(function(){

    /*
    $('.multiselect').multiSelect({ selectableOptgroup: true });

    $.each(origem, function(i, val)
    {
        $('#origem').multiSelect('select', val.toString());
    })

    $.each(destino, function(i, val)
    {
        $('#destino').multiSelect('select', val.toString());
    })

*/

    $('.multiselect').multiSelect({
        selectableOptgroup: true,
        selectableHeader: "<input style='border: 1px solid #CCC;padding: 10px;' type='text' class='search-input form-control' autocomplete='off' placeholder='Busca'>",
        selectionHeader: "<input style='border: 1px solid #CCC;padding: 10px;' type='text' class='search-input form-control' autocomplete='off' placeholder='Busca'>",
        afterInit: function(ms){
            var that = this,
                $selectableSearch = that.$selectableUl.prev(),
                $selectionSearch = that.$selectionUl.prev(),
                selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

            that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                .on('keydown', function(e){
                    if (e.which === 40){
                        that.$selectableUl.focus();
                        return false;
                    }
                });

            that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                .on('keydown', function(e){
                    if (e.which == 40){
                        that.$selectionUl.focus();
                        return false;
                    }
                });
        },
        afterSelect: function(){
            this.qs1.cache();
            this.qs2.cache();
        },
        afterDeselect: function(){
            this.qs1.cache();
            this.qs2.cache();
        }
    });

    $.each(origem, function(i, val)
    {
        $('#origem').multiSelect('select', val.toString());
    })

    $.each(destino, function(i, val)
    {
        $('#destino').multiSelect('select', val.toString());
    })


});

/*
app.controller("OrigemDestino", function($scope, $http, $filter)
{
    var localidades = [];

    $("#origem option").each(function()
    {
        localidades.push({name : $(this).html(), id : $(this).val()});
    })

    $scope.filtrar = function(filter)
    {
        $("#origem option").each(function(){

            if($(this).html() != filter)
            {
                console.log($(this).val());
                $("#origem").remove($(this).val());
            }

        })
        $("#origem").multiSelect('destroy');
        $("#origem").multiSelect();

    }

});

*/