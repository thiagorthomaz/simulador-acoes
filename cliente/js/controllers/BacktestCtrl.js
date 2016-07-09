app.controller("BacktestIFRCtrl", function ($scope, AcoesAPI, $filter) {
  
  $scope.valor_investido = 3000;
  
  AcoesAPI.simularIFR().success(function(r){
     console.log(r.conteudo.Trade[0]);
    $scope.acoes_simuladas = r.conteudo.Trade;
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