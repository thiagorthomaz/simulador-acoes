<?php

namespace app\model;

/**
 * Description of Trade
 *
 * @author thiago
 */
class Trade implements \stphp\ArraySerializable{

  private $cotacao;
  private $lotes;
  private $data_operacao;
  private $valor;
  private $tipo_trade; //Compra/Venda


  /**
   *
   * @var \app\model\Carteira;
   */
  private $carteira;
  
  public function __construct(\app\model\Carteira $carteira) {
    $this->carteira = clone $carteira;
  } 

  
  function getCotacao() {
    return $this->cotacao;
  }

  function getLotes() {
    return $this->lotes;
  }

  function getData_operacao() {
    return $this->data_operacao;
  }

  function getValor() {
    return $this->valor;
  }

  function getTipo_trade() {
    return $this->tipo_trade;
  }

  function getCarteira() {
    return $this->carteira;
  }

  function setCotacao($cotacao) {
    $this->cotacao = $cotacao;
  }

  function setLotes($lotes) {
    $this->lotes = $lotes;
  }

  function setData_operacao($data_operacao) {
    $this->data_operacao = $data_operacao;
  }

  function setValor($valor) {
    $this->valor = $valor;
  }

  function setTipo_trade($tipo_trade) {
    $this->tipo_trade = $tipo_trade;
  }

  function setCarteira(\app\model\Carteira $carteira) {
    $this->carteira = $carteira;
  }
  
  public function arraySerialize(){
    //$field_list = get_object_vars($this);
    $field_list = array(
      'cotacao', 'lotes', 'data_operacao', 'valor', 'tipo_trade', 'carteira'
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
  
  function comprarLotes() {
    
    
    $cotacao = $this->cotacao["fechamento"];
    $saldo_carteira = $this->carteira->getSaldo();    
    $lotes_comprados = floor(($saldo_carteira / $cotacao)/100) *100;
    $this->valor = $lotes_comprados * $this->cotacao["fechamento"];

    $this->setLotes($lotes_comprados);
    $this->carteira->comprar($lotes_comprados, $cotacao);
  }
  
  function venderLotes($lotes_venda_parcial = 0){
    
    if ($lotes_venda_parcial === 0){
      $this->lotes = $this->carteira->getLotes();
    } else {
      $this->lotes = $lotes_venda_parcial;
    }

    $cotacao = $this->cotacao["fechamento"];
    $this->valor = ($this->lotes * $cotacao);     
    $this->carteira->vender( $this->lotes, $cotacao );

  }
  
}
