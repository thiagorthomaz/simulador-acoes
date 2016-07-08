app.factory("AcoesAPI", function($http,config){

    return {
        simularIFR : function(){
            return $http.get(config.baseUrl + "Ativo.simularIFR");
        }
    };
    
});