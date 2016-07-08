app.controller("BacktestIFRCtrl", function ($scope, AcoesAPI, $filter) {
  
  $scope.valor_investido = 3000;
  
  AcoesAPI.simularIFR().success(function(r){
    $scope.acoes_simuladas = r.conteudo;
  });
  
  $scope.calcularLotes = function(valor_investido, fechamento){
    var qtde_lotes = valor_investido / fechamento;
    var qtde_lotes_inteiros = $filter('Floor')(qtde_lotes,100);  
    return qtde_lotes_inteiros;
  };
  
  $scope.calcularValorCompra = function(qtde_lotes, fechamento){
    $scope.valor_compra = qtde_lotes * fechamento;
  };
  
});