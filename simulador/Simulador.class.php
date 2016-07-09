<?php

namespace app\simulador;

/**
 * Description of Simulador
 *
 * @author thiago
 */
class Simulador {

  /**
   *
   * @var \app\setup\iSetup
   */
  protected $setup;
  protected $trades = array();
  /**
   *
   * @var \app\model\Carteira
   */
  protected $carteira;
          
  function __construct(\app\setup\iSetup $setup, \app\model\Carteira $carteira) {
    $this->setup = $setup;
    $this->carteira = $carteira;
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
            
            $trade = new \app\model\Trade($this->carteira);
            $trade->setCotacao_compra($cotacao);
            $trade->comprarLotes();

          }
        } else {
          
          $vender = $this->setup->avaliarvenda($cotacao);  
          
          if ($vender) {
            $this->setup->vender();
            $trade->setCotacao_venda($cotacao);
            $trade->venderLotes();
            $this->trades[] = $trade;
            $this->carteira = $trade->getCarteira();

            $trade = null;
          }          
        }

      }
    }
    
    //print_r($this->trades[0]);
    
  }
  
  public function getResultados(){
    return $this->trades;
  }
  
  
}
