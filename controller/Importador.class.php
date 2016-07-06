<?php

namespace app\controller;

/**
 * Description of Importador
 *
 * @author thiago
 */
class Importador extends \stphp\Controller {
  
  public function importarBase(){
    
    ini_set('memory_limit', '1024M'); 
    
    
    $dao = new \app\model\PrecoDAO();
    
    //$anos = array('2005','2006','2007','2008');
    $anos = array('2009','2010','2011','2012','2013','2014','2015');
    
    
    foreach ($anos as $ano){
      echo "\nIniciado a importação do ano de " . $ano;
      $filename = "COTAHIST_A" . $ano . ".TXT";
      $imp_cotacoes = new \app\model\ImportadorCotacoes($dao, $ano);
      $imp_cotacoes->setPath("/var/www/html/simulador-acoes/cotahist/");
      $imp_cotacoes->importaArquivo($filename);  
      echo "\nFinalizado a importação do ano de " . $ano;
    }
    
    echo "-------------- CONCLUÍDO! -------------------";
    exit;
  }
  
}
