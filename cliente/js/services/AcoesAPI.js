app.factory("AcoesAPI", function($http,config){

    return {
        simularIFR : function(cod_ativo){
            return $http.get(config.baseUrl + "Ativo.simularIFR&cod_ativo=" + cod_ativo);
        },
        listaCodigosAtivos : function(){
            return $http.get(config.baseUrl + "Ativo.listaCodigosAtivos");
        },
        analisadorDiario : function () {
          return $http.get(config.baseUrl + "Ativo.analisadorDiario");
        }
    };
    
});