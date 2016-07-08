<?php

namespace app\setup;


/**
 * Description of iSetup
 *
 * @author thiago
 */
interface iSetup {
  
  public function avaliarCompra($preco);
  public function avaliarvenda($preco);
  public function comprado();
  public function vendido();
  public function comprar();
  public function vender();
  public function getCriterioCompra();
  public function getCriterioVenda();
  public function getPeriodo();
  
  
  
}
