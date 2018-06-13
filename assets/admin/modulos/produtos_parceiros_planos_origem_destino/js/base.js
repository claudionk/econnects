

$(function()
{
    $('.multiselect').multiSelect({ selectableOptgroup: true });

    $.each(origem, function(i, val)
    {
        $('#origem').multiSelect('select', val.toString());
    })

    $.each(destino, function(i, val)
    {
        $('#destino').multiSelect('select', val.toString());
    })
})
