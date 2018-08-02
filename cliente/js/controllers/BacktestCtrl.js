app.controller("BacktestIFRCtrl", function ($scope, AcoesAPI, $stateParams) {

  var cod_ativo = $stateParams.cod_ativo;
  simularAtivo(cod_ativo);
  $scope.cod_ativo = cod_ativo;
  
  $scope.carteira_simular = {
    periodo : 200,
    criterio_compra : 30,
    criterio_venda : 80,
    saldo_inicial : 4000,
  };
  
  function simularAtivo(_carteira_simular_){
    if (_carteira_simular_) {
      console.log(_carteira_simular_);
      
      var cod_ativo = _carteira_simular_.cod_ativo.cod_ativo;
      var criterio_ifr_compra = _carteira_simular_.criterio_compra;
      var criterio_ifr_venda = _carteira_simular_.criterio_venda;
      var periodo = _carteira_simular_.periodo;

      AcoesAPI.simularIFR(cod_ativo, criterio_ifr_compra, criterio_ifr_venda, periodo).success(function(r){
        $scope.acoes_simuladas = r.conteudo.Operacao;
        $scope.carteira_inicial = r.conteudo.carteira_inicial;
        $scope.carteira_final = r.conteudo.carteira_final;
      });  
    }
    
  };

  AcoesAPI.listaCodigosAtivos().success(function(r){
    $scope.lista_codigos_ativos = r.conteudo.lista_ativos;
  });
  
  $scope.simularAtivo = simularAtivo;
  
});