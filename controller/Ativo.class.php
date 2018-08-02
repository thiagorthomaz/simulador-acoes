<?php

namespace app\controller;
/**
 * Description of Ativo
 *
 * @author thiago
 */
class Ativo extends \stphp\Controller {
  
  public function getAtivo($codigo_ativo, $mms1){
    $preco_dao = new \app\model\PrecoDAO();
    $rs = $preco_dao->precaoAtivo($codigo_ativo, $mms1);
    return $rs;
  }
  
  
  /**
   * index.php?Ativo.simularIFR&cod_ativo=ABEV3&criterio_ifr_compra=30&criterio_ifr_venda=80&periodo=200
   * @return \app\view\RespostaJson
   */
  public function simularIFR(){
    
    $request = $this->getRequest();
    
    $cod_ativo = $request->getParams("cod_ativo");
    $saldo_inicio = $request->getParams("saldo_inicio");
    $criterio_ifr_compra = $request->getParams("criterio_ifr_compra");
    $criterio_ifr_venda = $request->getParams("criterio_ifr_venda");
    $periodo_ifr = $request->getParams("periodo");
    $mms1 = $request->getParams("mms1");
    
    if (empty($periodo_ifr)) {
      $periodo_ifr = 2;
    }
    
    if (empty($criterio_ifr_compra)) {
      $criterio_ifr_compra = 30;
    }
    
    if (empty($criterio_ifr_venda)) {
      $criterio_ifr_compra = 80;
    }
    
    if (empty($saldo_inicio)) {
      $saldo_inicio = 4000;
    }
    
    $dados = $this->getAtivo($cod_ativo, $mms1);
    
    $carteira = new \app\model\Carteira($saldo_inicio);

    $setup_ifr = new \app\setup\SetupIFR($criterio_ifr_compra, $criterio_ifr_compra, $periodo_ifr);
    $simulador = new \app\simulador\Simulador($setup_ifr, $carteira, $mms1);
    $simulador->backTest($dados);
    $resultado = $simulador->getResultados();

    $carteira_inicial = $simulador->getCarteira_inicial();
    $carteira_final = $simulador->getCarteira_final();
    
    $resposta = new \app\view\RespostaJson();
    
    $resposta->addArray("carteira_inicial", $carteira_inicial->arraySerialize());
    $resposta->addArray("carteira_final", $carteira_final->arraySerialize());
    
    foreach ($resultado as $trade) {
      $resposta->addContent($trade, true);
    }

    return $resposta;

  }
  
  public function listaCodigosAtivos(){

    $preco_dao = new \app\model\PrecoDAO();
    $lista_ativos = $preco_dao->listarAtivos();

    $resposta = new \app\view\RespostaJson();
    $resposta->addArray("lista_ativos", $lista_ativos);
    return $resposta;
    
  }
  
  public function analisadorDiario() {
    
    $lista_possiveis_trades = array();
    $lista_ativos_compra = array();
    $lista_ativos_venda = array();
    
    $setup_ifr = new \app\setup\SetupIFR(30, 80);
    
    $preco_dao = new \app\model\PrecoDAO();
    $lista_ativos = $preco_dao->listarAtivos();

    $data_ontem = date("Y-m-d", strtotime("-1 day"));
    
    $calendario = new \app\model\Calendario();
    $dia_util = $calendario->getUltimoDiaUtil($data_ontem);
    $dia_util = date("Y-m-d", strtotime($dia_util));
    $data_hoje = date("Y-m-d");

    foreach ($lista_ativos as $ativo) {
      $cod_ativo = $ativo['cod_ativo'];
      $dados = $this->getAtivo($cod_ativo);
      
      $ifr = new \app\estudos\IFR($dados);
      $ifr->calcula(2);
      $precos_calculados = $ifr->getResultado();
      
      foreach (array_reverse($precos_calculados) as $cotacao) {
        if ($cotacao['data_pregao'] == $dia_util || $cotacao['data_pregao'] == $data_hoje) {
          $comprar = $setup_ifr->avaliarCompra($cotacao);
          $vender = $setup_ifr->avaliarVenda($cotacao);
          if ($comprar){
            $lista_ativos_compra[] = $cotacao;
          }
          
          if ($vender){
            $lista_ativos_venda[] = $cotacao;
          }
          
          break;

        }        
        
      } 
    }
    
    $lista_possiveis_trades["compras"] = $lista_ativos_compra;;
    $lista_possiveis_trades["venda"] = $lista_ativos_venda;
    
    $resposta = new \app\view\RespostaJson();
    $resposta->addArray("lista_ativos", $lista_possiveis_trades);
    return $resposta;
 
  }
  
}
