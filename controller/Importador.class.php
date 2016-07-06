<?php

namespace app\controller;

/**
 * Description of Importador
 *
 * @author thiago
 */
class Importador extends \stphp\Controller {
  
  public function teste(){
    
    ini_set('memory_limit', '1024M'); 
    
    
    $dao = new \app\model\PrecoDAO();
    
    $filename = "COTAHIST_A2015.TXT";
    $imp_cotacoes = new \app\model\ImportadorCotacoes($dao);
    $imp_cotacoes->setPath("/var/www/html/simulador-acoes/cotahist/");
    $imp_cotacoes->importaArquivo($filename);
    exit;
  }
  
}
