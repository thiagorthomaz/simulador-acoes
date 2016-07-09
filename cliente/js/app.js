
var app = angular.module("acoes", [
  'ui.router'
]);

app.config(function($stateProvider, $urlRouterProvider) {
  //
  // For any unmatched url, redirect to /state1
  $urlRouterProvider.otherwise("/backtest_ifr/ABEV3");
  //
  // Now set up the states
  $stateProvider
    .state('backtest_ifr', {
      url: "/backtest_ifr/:cod_ativo",
      controller: "BacktestIFRCtrl",
      templateUrl: "partials/backtest_ifr.html"
    });
});