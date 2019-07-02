// Seta APP para Angular JS
var app = angular.module("App", []);

app.directive('onFinishRender', function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attr) {
            if (scope.$last === true)
            {
                $timeout(function () {
                    scope.$emit(attr.onFinishRender);
                });
            }
        }
    }
});


app.controller("Enquete", function($scope, $http)
{
    $scope.perguntas = [];


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

    $scope.get_opcoes = function(i)
    {

        if(typeof $scope.perguntas[i].opcoes_1 == typeof undefined)
            $scope.perguntas[i].opcoes_1 = '';

        if(typeof $scope.perguntas[i].opcoes_2 == typeof undefined)
            $scope.perguntas[i].opcoes_2 = '';

        if(typeof $scope.perguntas[i].opcoes_3 == typeof undefined)
            $scope.perguntas[i].opcoes_3 = '';


        if($scope.perguntas[i].tipo == 'zero_a_dez')
            return $scope.perguntas[i].opcoes_1 + ',' + $scope.perguntas[i].opcoes_2 + ',' + $scope.perguntas[i].opcoes_3;

    }

    $scope.get_opcoes_valor = function(i, opcao)
    {

        console.log('OPC', i, opcao);

        if(opcao == 1){
            return $scope.perguntas[i].opcoes_1;
        }else if(opcao == 2){
            return $scope.perguntas[i].opcoes_2;
        }else if(opcao == 3){
            return $scope.perguntas[i].opcoes_3;

        }
        /*
        var opcoes = $scope.perguntas[i].opcoes.split(",");

        console.log(opcoes[opcao-1]);

        return opcoes[opcao-1];*/
    }
})