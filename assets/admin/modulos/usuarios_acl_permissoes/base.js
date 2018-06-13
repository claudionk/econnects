$(function(){


    $('.btRecurso').click(function()
    {
        var paiId = $(this).closest(".filhos").attr("id");
        var btPai = $(".btRecurso[idRecurso='" + paiId + "'][idAcao='1']");
        var idRecurso = $(this).attr("idRecurso");

        var idAcao = $(this).attr("idAcao");

        if($(this).prop("checked"))
        {
            $('input[idRecurso="' + idRecurso + '"][idAcao="1"]').prop("checked", true);

            if(!btPai.prop("checked"))
            {
                btPai.trigger("click");
            }
        }
        else
        {
            $(".filhos[id='" +idRecurso + "']").find("input").prop("checked", false);

            if(idAcao == 1)
            {
                $("input[idRecurso='" +idRecurso + "']").prop("checked", false);
            }
        }
    })
})