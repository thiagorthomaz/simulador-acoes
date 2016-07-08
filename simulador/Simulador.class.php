<?php

namespace app\simulador;

/**
 * Description of Simulador
 *
 * @author thiago
 */
class Simulador {

  protected $setup;
  protected $trades = array();
  function __construct(\app\setup\iSetup $setup) {
    $this->setup = $setup;
  }

  
  public function backTest($dados){
    
    $periodo = $this->setup->getPeriodo();
    
    $ifr = new \app\estudos\IFR($dados);
    $ifr->calcula($periodo);
    
    $precos_calculados = $ifr->getResultado();

    $trade = null;

    foreach ($precos_calculados as $cotacao) {
      
      if (isset($cotacao['ifr'])) {
        $comprado = $this->setup->comprado();
        if (!$comprado){
          $comprar = $this->setup->avaliarCompra($cotacao);
          if ($comprar){
            $this->setup->comprar();
            $trade = new \app\model\Trade();
            $trade->setCotacao_compra($cotacao);
            
          }
        } else {
          
          $vender = $this->setup->avaliarvenda($cotacao);  
          
          if ($vender) {
            $trade->setCotacao_venda($cotacao);
            $this->setup->vender();
            $this->trades[] = $trade;
            $trade = null;
          }          
        }

      }
    }

  }
  
  public function getResultados(){
    return $this->trades;
  }
  
  
}
