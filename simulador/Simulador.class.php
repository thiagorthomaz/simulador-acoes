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
  }

  
  public function backTest($dados){
    
    $periodo = $this->setup->getPeriodo();
    
    $ifr = new \app\estudos\IFR($dados);
    $ifr->calcula($periodo);
    
    $precos_calculados = $ifr->getResultado();
    
    $operacao = null;
    
    foreach ($precos_calculados as $cotacao) {
      
      if (isset($cotacao['ifr'])) {
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
            
            
            $operacao->setTrade_venda($trade);
            $operacao->getRealizado();
            $operacao->getRentabilidade();
            $this->operacoes[] = $operacao;

          }          
        }

      }
    }

    $this->carteira_final = clone $this->carteira;

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
