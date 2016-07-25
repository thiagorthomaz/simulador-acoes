<?php

namespace app\simulador;

/**
 * Description of Simulador
 *
 * @author thiago
 */
class Simulador {

  /**
   *
   * @var \app\setup\iSetup
   */
  protected $setup;
  protected $trades = array();
  protected $operacoes = array();
  /**
   *
   * @var \app\model\Carteira
   */
  protected $carteira;
  /**
   *
   * @var \app\model\Carteira
   */
  protected $carteira_inicial;
  /**
   *
   * @var \app\model\Carteira
   */
  protected $carteira_final;
          
  function __construct(\app\setup\iSetup $setup, \app\model\Carteira $carteira) {
    $this->setup = $setup;
    $this->carteira = $carteira;
    $this->carteira_inicial = clone $carteira;
    $this->carteira_final = new \app\model\Carteira(0);
  }

  
  public function backTest($dados){
    
    $periodo = $this->setup->getPeriodo();
    $periodo_mms = 200;
    
    $mms = new \app\estudos\MMS($dados, $periodo_mms);
    $mms->calcula();
    $mms_calulados = $mms->getResultado();
    
    $ifr = new \app\estudos\IFR($mms_calulados);
    $ifr->calcula($periodo);

    $precos_calculados = $ifr->getResultado();
    $pulos = range(0, $periodo);

    
    foreach ( $pulos as $i  ){
      array_shift($precos_calculados);
    }
        
    $operacao = null;

    foreach ($precos_calculados as $i => $cotacao) {

      $comprado = $this->setup->comprado();
      if (!$comprado){
        $comprar = $this->setup->avaliarCompra($cotacao);
        if ($comprar){

          $this->setup->comprar();
          $trade = new \app\model\Trade($this->carteira);
          $trade->setCotacao($cotacao);
          $trade->setTipo_trade("Compra");
          $trade->setData_operacao(date("Y-m-d H:i:s"));
          $trade->comprarLotes();

          $operacao = new \app\model\Operacao();
          $operacao->setTrade_compra($trade);

          $this->carteira = $trade->getCarteira();

        }
      } else {

        $vender = $this->setup->avaliarvenda($cotacao);  

        if ($vender) {
          $this->setup->vender();

          $trade = new \app\model\Trade($this->carteira);
          $trade->setCotacao($cotacao);
          $trade->setTipo_trade("Venda");
          $trade->setData_operacao(date("Y-m-d H:i:s"));
          $trade->venderLotes();
          $this->carteira = $trade->getCarteira();
          $this->carteira_final = clone $this->carteira;
          $mms_anterior = $precos_calculados[$i-1]["mms"];
          $mms_atual = $cotacao["mms"];

          if ($mms_atual > $mms_anterior){
            $operacao->setMms("Cima");
          }
          
          if ($mms_atual < $mms_anterior){
            $operacao->setMms("Baixo");
          }
          
          if ($mms_atual === $mms_anterior){
            $operacao->setMms("Igual");
          }
          
          $operacao->setTrade_venda($trade);
          $operacao->getRealizado();
          $operacao->getRentabilidade();
          $this->operacoes[] = $operacao;

        }          
      }

    }

  }
  
  function getCarteira_inicial() {
    return $this->carteira_inicial;
  }

  function getCarteira_final() {
    return $this->carteira_final;
  }
  
  public function getResultados(){
    return $this->operacoes;
  }
  
  
}
