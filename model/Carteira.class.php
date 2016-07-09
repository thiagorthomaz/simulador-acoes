<?php

namespace app\model;

/**
 * Description of Carteira
 *
 * @author thiago
 */
class Carteira implements \stphp\ArraySerializable {

  private $saldo;
  
  function __construct() {
    $this->saldo = 4000;
  }

  
  function getSaldo() {
    return $this->saldo;
  }

  function setSaldo($saldo) {
    $this->saldo = $saldo;
  }
  
  function debitar($valor){
    $this->saldo -= $valor;
  }
  
  function creditar($valor){
    $this->saldo += $valor;
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
