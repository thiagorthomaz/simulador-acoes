app.controller("BacktestIFRCtrl", function ($scope, AcoesAPI, $stateParams) {

  var cod_ativo = $stateParams.cod_ativo;
  simularAtivo(cod_ativo);
  $scope.cod_ativo = cod_ativo;
  
  $scope.cod_ativo_selecionado = {"cod_ativo":cod_ativo};
  function simularAtivo(cod_ativo){
      AcoesAPI.simularIFR(cod_ativo).success(function(r){
        $scope.acoes_simuladas = r.conteudo.Operacao;
        $scope.carteira_inicial = r.conteudo.carteira_inicial;
        $scope.carteira_final = r.conteudo.carteira_final;
      });
  };

  AcoesAPI.listaCodigosAtivos().success(function(r){
    $scope.lista_codigos_ativos = r.conteudo.lista_ativos;
  });
  
  $scope.simularAtivo = function(ativo){
    simularAtivo(ativo.cod_ativo);
    $scope.cod_ativo = ativo.cod_ativo;
  };
  
});