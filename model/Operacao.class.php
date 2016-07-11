<?php

namespace app\model;

/**
 * Description of Operacao
 *
 * @author thiago
 */
class Operacao implements \stphp\ArraySerializable {
  
  /**
   *
   * @var \app\model\Trade
   */
  private $trade_compra;
  /**
   *
   * @var \app\model\Trade
   */
  private $trade_venda;
  
  private $rentabilidade;
  
  private $realizado;

  
  function getTrade_compra() {
    return $this->trade_compra;
  }

  function getTrade_venda() {
    return $this->trade_venda;
  }

  function getRentabilidade() {
    $valor_compra = $this->trade_compra->getValor();
    $this->rentabilidade = ($this->realizado / ($valor_compra /100) ) *100;
    return $this->rentabilidade;
  }

  function getRealizado() {

    $cotacao_compra = $this->trade_compra->getCotacao();
    $cotacao_venda = $this->trade_venda->getCotacao();
    $this->realizado = $cotacao_venda['fechamento'] - $cotacao_compra['fechamento'];
    return $this->realizado;
  }

  function setTrade_compra($trade_compra) {
    $this->trade_compra = $trade_compra;
  }

  function setTrade_venda($trade_venda) {
    $this->trade_venda = $trade_venda;
  }

  public function arraySerialize() {
    $field_list = array(
      'trade_venda', 'trade_compra', 'rentabilidade', 'realizado'
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
  
}
