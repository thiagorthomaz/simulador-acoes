<?php

namespace app\model;

/**
 * Description of Trade
 *
 * @author thiago
 */
class Trade implements \app\model\ArraySerializable{

  private $cotacao_compra;
  private $cotacao_venda;
  

  function getCotacao_compra() {
    return $this->cotacao_compra;
  }

  function getCotacao_venda() {
    return $this->cotacao_venda;
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
    $field_list = array('cotacao_compra', 'cotacao_venda');
    return $this->toArray($this, $field_list);
    
  }
  
  public function toArray($obj, $field_list){
    $array = array();
    foreach ($field_list as $field) {
      $array[$field] = call_user_func(array($obj, "get" . $field));
    }
    return $array;
  }
  
}
