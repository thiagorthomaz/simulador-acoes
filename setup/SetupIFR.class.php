<?php

namespace app\setup;

/**
 * Description of IFR
 *
 * @author thiago
 */
class SetupIFR implements \app\setup\iSetup {
  
  private $criterio_compra;
  private $criterio_venda;
  private $comprado = false;
  private $periodo;
  private $dados;
  private $resultado;
  
  public function __construct(\app\model\Criterio $criterio, $dados) {
    $this->criterio_compra = $criterio->getCriterio_compra();
    $this->criterio_venda = $criterio->getCriterio_venda();
    $this->periodo = $criterio->getPeriodo();
    $this->dados = $dados;
  }

  public function avaliarvenda($preco) {
    if ($this->comprado) {
      if ($preco["ifr"] >= $this->criterio_venda){
        return true;
      }
    }
    
    return false;
  }

  public function avaliarCompra($preco) {
    if (!$this->comprado) {
      if ($preco["ifr"] <= $this->criterio_compra && $preco["ifr"] > 0){
        return true;
      }
    }
    return false;
  }

  public function comprado() {
    return $this->comprado;
  }

  public function vendido() {
    return false;
  }

  public function getCriterioCompra() {
    return $this->criterio_compra;
  }

  public function getCriterioVenda() {
    return $this->criterio_venda;
  }

  public function comprar() {
    $this->comprado = true;
  }

  public function vender() {
    $this->comprado = false;
  }

  public function getPeriodo() {
    return $this->periodo;
  }
  
  public function executar(){
    
    $periodo = $this->getPeriodo();
    
    $ifr = new \app\estudos\IFR($this->dados);
    $ifr->calcula($periodo);

    $this->resultado = $ifr->getResultado();
    
    return $this->resultado;

  }

}
