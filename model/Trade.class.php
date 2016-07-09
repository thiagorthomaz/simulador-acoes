<?php

namespace app\model;

/**
 * Description of Trade
 *
 * @author thiago
 */
class Trade implements \stphp\ArraySerializable{

  private $cotacao_compra;
  private $cotacao_venda;
  
  private $lotes_comprados;
  private $lotes_vendidos;
  
  private $investido;
  private $retorno;
  
  private $realizado;
  
  private $rentabilidade;


  /**
   *
   * @var \app\model\Carteira;
   */
  private $carteira;
  
  public function __construct(\app\model\Carteira $carteira) {
    $this->carteira = clone $carteira;
  } 
  
  function getCotacao_compra() {
    return $this->cotacao_compra;
  }

  function getCotacao_venda() {
    return $this->cotacao_venda;
  }
  
  function getRentabilidade() {
    return $this->rentabilidade;
  }

  function setRentabilidade($rentabilidade) {
    $this->rentabilidade = $rentabilidade;
  }
  
  /**
   * @TODO usar objeto Cotacao
   * @param type $cotacao_compra
   */
  function setCotacao_compra($cotacao_compra) {
    $this->cotacao_compra = $cotacao_compra;
  }

  function setCotacao_venda($cotacao_venda) {
    $this->cotacao_venda = $cotacao_venda;
  }
  
  public function arraySerialize(){
    //$field_list = get_object_vars($this);
    $field_list = array(
      'cotacao_compra', 'cotacao_venda', 'lotes_comprados', 'lotes_vendidos',
      'investido', 'carteira', 'realizado', 'retorno', 'rentabilidade'
    );
    return $this->toArray($this, $field_list);
    
  }
  
  public function toArray($obj, $field_list){
    $array = array();
    foreach ($field_list as $field) {
      if ($this->$field instanceof \stphp\ArraySerializable){
        $array[$field] = $this->$field->arraySerialize();
      } else {
        $array[$field] = call_user_func(array($obj, "get" . $field));
      }
      
    }
    return $array;
  }

  function getLotes_comprados() {
    return $this->lotes_comprados;
  }

  function getLotes_vendidos() {
    return $this->lotes_vendidos;
  }

  function getRealizado() {
    return $this->realizado;
  }

  function getInvestido() {
    return $this->investido;
  }

  function getCarteira() {
    return $this->carteira;
  }

  function getRetorno() {
    return $this->retorno;
  }

  function setRetorno($retorno) {
    $this->retorno = $retorno;
  }
  
  function setInvestido($investido) {
    $this->investido = $investido;
  }

  function setCarteira(\app\model\Carteira $carteira) {
    $this->carteira = $carteira;
  }
  
  function setLotes_comprados($lotes_comprados) {
    $this->lotes_comprados = $lotes_comprados;
  }

  function setLotes_vendidos($lotes_vendidos) {
    $this->lotes_vendidos = $lotes_vendidos;
  }

  function setRealizado($realizado) {
    $this->realizado = $realizado;
  }
  
  function comprarLotes() {
    $saldo_carteira = $this->carteira->getSaldo();
    $lotes_comprados = floor(($saldo_carteira / $this->cotacao_compra["fechamento"])/100) *100;
    $investido = $lotes_comprados * $this->cotacao_compra["fechamento"];
    $this->setLotes_comprados($lotes_comprados);
    $this->setInvestido($investido);
    $this->carteira->debitar($investido);
  }
  
  function venderLotes(){
    $lotes_comprados = $this->getLotes_comprados();
    $this->setLotes_vendidos($lotes_comprados);
    $this->retorno = ($lotes_comprados * $this->cotacao_venda["fechamento"]);
    $realizado = $this->retorno - $this->getInvestido() ;
    $this->setRealizado($realizado);
    if ($this->investido <> 0){
      $this->rentabilidade =  ($realizado / $this->investido) * 100;
    } else {
      $this->rentabilidade = 0;
    }
    
    $this->carteira->creditar( $this->retorno );
  }
  
}
