app.controller("BacktestIFRCtrl", function ($scope, AcoesAPI) {
  
  $scope.valor_investido = 3000;
  
  AcoesAPI.simularIFR().success(function(r){
    $scope.acoes_simuladas = r.conteudo;
  });
  
  $scope.calcularLotes = function(valor_investido, fechamento){
    $scope.qtde_lotes = valor_investido / fechamento;
  };
  
  $scope.calcularValorCompra = function(qtde_lotes, fechamento){
    $scope.valor_compra = qtde_lotes * fechamento;
  };
  
});