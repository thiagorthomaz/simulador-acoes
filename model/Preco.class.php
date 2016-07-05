<?php

/**
 * Description of Preco
 *
 * @author thiago
 */
class Preco {

  private $DATA_PREGA;
  private $COD_ATIVO;
  private $ABERTURA;
  private $MAXIMA;
  private $MINIMA;
  private $MEDIO;
  private $FECHAMENTO;
  private $NEGOCIOS;
  private $VOLUME_FINANCEIRO;
  private $DATA_IMPORTACAO;

  function getDATA_PREGA() {
    return $this->DATA_PREGA;
  }

  function getCOD_ATIVO() {
    return $this->COD_ATIVO;
  }

  function getABERTURA() {
    return $this->ABERTURA;
  }

  function getMAXIMA() {
    return $this->MAXIMA;
  }

  function getMINIMA() {
    return $this->MINIMA;
  }

  function getMEDIO() {
    return $this->MEDIO;
  }

  function getFECHAMENTO() {
    return $this->FECHAMENTO;
  }

  function getNEGOCIOS() {
    return $this->NEGOCIOS;
  }

  function getVOLUME_FINANCEIRO() {
    return $this->VOLUME_FINANCEIRO;
  }

  function getDATA_IMPORTACAO() {
    return $this->DATA_IMPORTACAO;
  }

  function setDATA_PREGA($DATA_PREGA) {
    $this->DATA_PREGA = $DATA_PREGA;
  }

  function setCOD_ATIVO($COD_ATIVO) {
    $this->COD_ATIVO = $COD_ATIVO;
  }

  function setABERTURA($ABERTURA) {
    $this->ABERTURA = $ABERTURA;
  }

  function setMAXIMA($MAXIMA) {
    $this->MAXIMA = $MAXIMA;
  }

  function setMINIMA($MINIMA) {
    $this->MINIMA = $MINIMA;
  }

  function setMEDIO($MEDIO) {
    $this->MEDIO = $MEDIO;
  }

  function setFECHAMENTO($FECHAMENTO) {
    $this->FECHAMENTO = $FECHAMENTO;
  }

  function setNEGOCIOS($NEGOCIOS) {
    $this->NEGOCIOS = $NEGOCIOS;
  }

  function setVOLUME_FINANCEIRO($VOLUME_FINANCEIRO) {
    $this->VOLUME_FINANCEIRO = $VOLUME_FINANCEIRO;
  }

  function setDATA_IMPORTACAO($DATA_IMPORTACAO) {
    $this->DATA_IMPORTACAO = $DATA_IMPORTACAO;
  }

}
