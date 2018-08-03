<?php


namespace app\model;

/**
 * Description of Criterio
 *
 * @author thiago
 */
class Criterio {
  
  private $ativo;
  private $criterio_compra;
  private $criterio_venda;
  private $periodo;
  private $saldo_inicio;
  
  
  function __construct($ativo, $criterio_compra, $criterio_venda, $periodo, $saldo_inicio) {
    $this->ativo = $ativo;
    $this->criterio_compra = $criterio_compra;
    $this->criterio_venda = $criterio_venda;
    $this->periodo = $periodo;
    $this->saldo_inicio = $saldo_inicio;
  }
  
  
  function getAtivo() {
    return $this->ativo;
  }

  function getCriterio_compra() {
    return $this->criterio_compra;
  }

  function getCriterio_venda() {
    return $this->criterio_venda;
  }

  function getPeriodo() {
    return $this->periodo;
  }

  function getSaldo_inicio() {
    return $this->saldo_inicio;
  }
  
  
}
