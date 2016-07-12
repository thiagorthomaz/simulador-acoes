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
    
    //$anos = array('2009','2010','2011','2012','2013','2014','2015');
    $anos = array('2016');
    
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
  
  public function importarProventos(){
    
    
    
    
    $preco_dao = new \app\model\PrecoDAO();
    $lista_ativos = $preco_dao->listarAtivos();

    foreach ($lista_ativos as $ativo) {
      $cod_ativo = $ativo["cod_ativo"];

      $destino = "/var/www/html/simulador-acoes/cotahist/proventos/" . $cod_ativo . ".html";
      $url_fonte = "http://www.guiainvest.com.br/provento/default.aspx?sigla=" . $cod_ativo;
      
      $handle_importacao = fopen($destino, "w+");
      if (!$handle_importacao){
        echo "Erro ao criar o arquivo de destino";
        exit;
      }

      $handle_fonte = @fopen($url_fonte, "r");
      if ($handle_fonte) {
          $gravando = false;
          while (($buffer = fgets($handle_fonte, 4096)) !== false) {
            if (trim($buffer) == '<div id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao" class="RadGrid RadGrid_Padrao">'){
              $gravando = true;
            }
            if ($gravando){
              $buffer = str_replace('<table cellspacing="0" class="rgMasterTable" border="0" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00" style="width:100%;table-layout:auto;empty-cells:show;">', '<table>', $buffer);
              $buffer = str_replace('<colgroup>', '', $buffer);
              $buffer = str_replace('<col  />', '', $buffer);
              $buffer = str_replace('</colgroup>', '', $buffer);

              $buffer = str_replace('<tr class="rgRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__0">', '<tr>', $buffer);
              $buffer = str_replace('<tr class="rgAltRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__1">', '<tr>', $buffer);
              $buffer = str_replace('<tr class="rgRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__2">', '<tr>', $buffer);
              $buffer = str_replace('<tr class="rgAltRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__3">', '<tr>', $buffer);

              
              $buffer = str_replace('<th scope="col" class="rgHeader" style="white-space:nowrap;text-align:center;">', '<th>', $buffer);
              $buffer = str_replace('<th scope="col" class="rgHeader">', '<th>', $buffer);
              $buffer = str_replace('<th scope="col" class="rgHeader tdMobile320" style="text-align:center;">', '<th>', $buffer);
              $buffer = str_replace('<td align="center" style="white-space:nowrap;">', '<td>', $buffer);
              $buffer = str_replace('<td class="tdMobile320" align="center" style="white-space:nowrap;">', '<td>', $buffer);
                            
              $buffer = str_replace('<input id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ClientState" name="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ClientState" type="hidden" />', '', $buffer);
              
              fwrite($handle_importacao, $buffer);  
            }


            if (trim($buffer) == '</div>' && $gravando){
              $gravando = false;
            }

          }

          if (!feof($handle_fonte)) {
              echo "Error: unexpected fgets() fail\n";
          }
          fclose($handle_importacao);
          fclose($handle_fonte);
      }
    }
    
    
    
    
    echo "Importação concluída!";
    
    exit;
  }
  
  
  //http://bvmf.bmfbovespa.com.br/InstDados/SerHist/COTAHIST_D11072016.ZIP
  public function importadorDiario(){
    
    echo date("dmY");
    
    
    exit;
    
  }
  
}
