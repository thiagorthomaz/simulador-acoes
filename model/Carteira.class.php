<?php

namespace app\model;

/**
 * Description of Carteira
 *
 * @author thiago
 */
class Carteira implements \stphp\ArraySerializable {

  private $saldo;
  private $lotes;
  
  function __construct($valor_inicial) {
    $this->saldo = $valor_inicial;
  }

  
  function getSaldo() {
    return $this->saldo;
  }

  private function setSaldo($saldo) {
    $this->saldo = $saldo;
  }
  
  private function debitar($valor){
    $this->saldo -= $valor;
  }
  
  private function creditar($valor){
    $this->saldo += $valor;
  }

  function getLotes() {
    return $this->lotes;
  }

  private function setLotes($lotes) {
    $this->lotes = $lotes;
  }

  function vender($lotes, $cotacao){
    $this->creditar($cotacao*$lotes);
    $this->lotes -= $lotes;
    
  }
  
  function comprar($lotes, $cotacao){
    $this->debitar($cotacao*$lotes);
    $this->lotes += $lotes;
  }
  
  public function arraySerialize(){
    //$field_list = get_object_vars($this);
    $field_list = array(
      'saldo'
    );
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
