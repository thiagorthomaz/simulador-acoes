app.factory("AcoesAPI", function($http,config){

    return {
        simularIFR : function(cod_ativo, criterio_ifr_compra, criterio_ifr_venda, periodo, mms1){
            return $http.get(config.baseUrl + "Ativo.simularIFR&cod_ativo=" + cod_ativo + "&criterio_ifr_compra=" + criterio_ifr_compra + "&criterio_ifr_venda=" + criterio_ifr_venda + "&periodo=" + periodo + "&mms1=" + mms1);
        },
        listaCodigosAtivos : function(){
            return $http.get(config.baseUrl + "Ativo.listaCodigosAtivos");
        },
        analisadorDiario : function () {
          return $http.get(config.baseUrl + "Ativo.analisadorDiario");
        }
    };
    
});