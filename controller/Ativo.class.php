<?php

namespace app\controller;
/**
 * Description of Ativo
 *
 * @author thiago
 */
class Ativo extends \stphp\Controller {
  
  public function getAtivo($codigo_ativo){
    $preco_dao = new \app\model\PrecoDAO();
    $rs = $preco_dao->precaoAtivo($codigo_ativo);
    return $rs;
  }
  
  
  public function simularIFR(){
    
    $dados = $this->getAtivo("ABEV3");
    
    $ifr = new \app\setup\IFR(30, 80);
    
    $ifr->simular($dados);
    $resultado = $ifr->getresultado();
    //print_r($resultado);
    
    $resposta = new \app\view\RespostaJson();
    $resposta->addArray($resultado);
    return $resposta;

  }
  
}
