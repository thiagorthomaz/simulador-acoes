<?php

namespace app\setup;

/**
 * Description of IFR
 *
 * @author thiago
 */
class IFR {
  
  private $criterio_compra;
  private $criterio_venda;
  private $comprado = false;
  
  private $trades = array();
  
  public function __construct($criterio_ifr_compra, $criterio_ifr_venda) {

    $this->criterio_compra = $criterio_ifr_compra;
    $this->criterio_venda = $criterio_ifr_venda;

  }

    public function simular($dados){
    
    $ifr = new \app\estudos\IFR($dados);
    $ifr->calcula(2);
    $precos_calculados = $ifr->getResultado();
    
    $index_trade = 0;
    
    foreach ($precos_calculados as $preco) {
      if (isset($preco['ifr'])) {
        $compra = $this->compra($preco);
        $venda = $this->venda($preco);
        
        if ($compra){
          $this->trades[$index_trade]['compra'] = $compra;
        }
        if ($venda){
          $this->trades[$index_trade]['venda'] = $venda;
          $index_trade++;
        }
        
      }

    }

  }
  
  private function compra($preco){
    if (!$this->comprado) {
      if ($preco["ifr"] < $this->criterio_compra && $preco["ifr"] > 0){
        $this->comprado = true;
        return $preco;
      }
    }
    
    return false;
  }
  
  private function venda($preco){
    if ($this->comprado) {
      if ($preco["ifr"] > $this->criterio_venda){
        $this->comprado = false;
        return $preco;
      }
    }

    return false;
    
  }
  
  public function getresultado(){
    return $this->trades;
  }
  
}
