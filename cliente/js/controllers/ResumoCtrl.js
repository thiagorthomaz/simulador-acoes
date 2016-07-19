app.controller("ResumoCtrl", function ($scope, AcoesAPI) {
  AcoesAPI.analisadorDiario().success(function(r){
    $scope.lista_acoes_compra = r.conteudo.lista_ativos.compras;
    $scope.lista_acoes_venda = r.conteudo.lista_ativos.venda;
    console.log(r.conteudo.lista_ativos.compras);
    
    
  });;


});