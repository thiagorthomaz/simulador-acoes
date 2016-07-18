<?php

namespace app\controller;

/**
 * Description of Importador
 *
 * @author thiago
 */
class Importador extends \stphp\Controller {

  public function importarBase() {

    ini_set('memory_limit', '1024M');


    $dao = new \app\model\PrecoDAO();

    $anos = array('2005','2006','2007','2008', '2009','2010','2011','2012','2013','2014','2015', '2016');

    foreach ($anos as $ano) {
      echo "\nIniciado a importação do ano de " . $ano;
      $filename = "COTAHIST_A" . $ano . ".TXT";
      $imp_cotacoes = new \app\model\ImportadorCotacoes($dao, $ano);
      $imp_cotacoes->setPath(CAMINHO_SISTEMA . "/cotahist/");
      $imp_cotacoes->importaArquivo($filename);
      echo "\nFinalizado a importação do ano de " . $ano;
    }

    echo "-------------- CONCLUÍDO! -------------------";
    exit;
  }

  function finalDesemana($data) {
    $dia_semana = date('w', strtotime($data));
    return ($dia_semana == 0 || $dia_semana == 6);
  }
  
  function feriado($data) {
    
    
    
  }
  
  public function desdobramentos(){
    
    $dao = new \app\model\Tab_hist_proventosDAO();
    $lista_proventos = $dao->getProventos();
    print_r($lista_proventos);exit;
    foreach ($lista_proventos as $provento) {
      $cod_ativo = $provento['cod_ativo'];
      $descricao = $provento['descricao'];

      $tipo = $provento['tipo'];
      $data = $provento['data'];

      if ($tipo == "Grupamento") {
        $split_desc = explode("/", trim($descricao));
        $divisor = str_replace(".", "", $split_desc[0]);
        $divisor = str_replace(",", ".", $split_desc[0]);
        if (count(explode(".", $divisor)) > 2){
          $divisor_split = explode(".", $divisor);
          $divisor = $divisor_split[0] . $divisor_split[1] . "." . $divisor_split[2];
        }

        $sql = "update Tab_preco set abertura=abertura*$divisor, maxima=maxima*$divisor, minima=minima*$divisor, medio=medio*$divisor, fechamento=fechamento*$divisor" ;
        $sql .= " where data_pregao<='$data' and cod_ativo='$cod_ativo'; \n";
        $update = "update Tab_hist_provento set atualizado = true where cod_ativo='$cod_ativo' and data='$data' and descricao='$descricao';\n";
        //echo "\n---Grupamento\n";
        echo $sql . $update;
        
      }
      
      if ($tipo == "Desdobramento") {
        $split_desc = explode(":", trim($descricao));
        $split_desc = explode(" ", trim($split_desc[1]));

        $fator = str_replace(".", "", $split_desc[0]);
        $fator = str_replace(",", ".", $split_desc[0]);
        if (count(explode(".", $fator)) > 2){
          $fator_split = explode(".", $fator);
          $fator = $fator_split[0] . $fator_split[1] . "." . $fator_split[2];
        }
        //echo "\n---Desdobramento\n";
        $sql = "update Tab_preco set abertura=abertura/$fator, maxima=maxima/$fator, minima=minima/$fator, medio=medio/$fator, fechamento=fechamento/$fator" ;
        $sql .= " where data_pregao<='$data' and cod_ativo='$cod_ativo'; \n";
        $update = "update Tab_hist_provento set atualizado = true where cod_ativo='$cod_ativo' and data='$data' and descricao='$descricao';\n";
        
        
        echo $sql . $update;
      }
            
    }

    exit;
  }
  
  public function agrupamentos(){
    
  }
  
  //http://bvmf.bmfbovespa.com.br/InstDados/SerHist/COTAHIST_D11072016.ZIP
  //
  public function importadorDiario() {
    
    $prefixo = "COTAHIST_D";
    $data_arquivo = date("dmY", strtotime("-1 day"));
    $data = date("Y-m-d", strtotime("-1 day"));
    $logfile_handle = fopen(CAMINHO_SISTEMA . "/log/log_" . $data_arquivo . ".txt", "a+");
    
    try {

      $url = "http://bvmf.bmfbovespa.com.br/InstDados/SerHist/";


      if (!$this->finalDesemana($data) && !$this->feriado($data)) {

        $dir_arquivo_diario = getcwd() . "/cotahist/arquivos_diarios/";
        //$source = "http://bvmf.bmfbovespa.com.br/InstDados/SerHist/COTAHIST_D11072016.ZIP";
        $url_arquivo_bovespa = $url . $prefixo . $data_arquivo . ".ZIP";
        $dir_arquivo_temporario = "/tmp/" . $prefixo . $data . ".ZIP";

        if (!copy($url_arquivo_bovespa, $dir_arquivo_temporario)){
          throw new \Exception("Falha ao salvar arquivo do dia: " . $data);
        }

        $zip = new \ZipArchive();
        $zip->open($dir_arquivo_temporario);
        $zip->extractTo($dir_arquivo_diario);
        $zip->close();

        $dao = new \app\model\PrecoDAO();
        $imp_cotacoes = new \app\model\ImportadorCotacoes($dao, $data_arquivo);
        $imp_cotacoes->setPath($dir_arquivo_diario);
        $imp_cotacoes->importaArquivo($prefixo . $data_arquivo . ".TXT");

        fwrite($logfile_handle, "Arquivo diário importado. \n");

      }
    }  catch (\Exception $e) {
      
      $texto = $e->getFile() . "\n Linha: " . $e->getLine() . ": " . $e->getMessage();
      fwrite($logfile_handle, $texto);
      fwrite($logfile_handle, "\n\n----------------------------------\n\n");
    }
    fclose($logfile_handle);
    exit;

  }
  
  public function importarProventosBase(){
    $preco_dao = new \app\model\PrecoDAO();
    $lista_ativos = $preco_dao->listarAtivos();

    $caminho = CAMINHO_SISTEMA . "/cotahist/proventos/";
    $caminho_arquivo = CAMINHO_SISTEMA . "/dump/hist_proventos.sql";

    $handle_hist = fopen($caminho_arquivo, "w+");
    
    foreach ($lista_ativos as $ativo) {
      $cod_ativo = $ativo["cod_ativo"];
      $caminho_completo = $caminho . $cod_ativo . ".html";
      
      
      $handle = fopen($caminho_completo, "r");
      if ($handle) {
        $body = false;
        while (($buffer = fgets($handle, 4096)) !== false) {
          $linha = trim(fgets($handle));
          if (strpos($linha, "<tbody>") !== false) {
            $body = true;
            continue;
          }
          
          if ($body) {
            $split = explode("<td>", $linha);
            unset($split[0]);
            if (count($split) === 4) {
              $tipo = str_replace("</td>", "", $split[1]);
              $data = str_replace("</td>", "", $split[2]);
              $split_date = explode("/", $data);
              if (count($split_date) === 1) {
                //"Se não tiver data, ignora";
                echo "Não gerado - " . $cod_ativo . "\n";
                continue;
              }
              $data = date('Y-m-d',  strtotime($split_date[2]. "-" . $split_date[1] . "-" . $split_date[0]));
              $descricao = str_replace("</td>", "", $split[3]);
              
              $sql = "insert into Tab_hist_provento (cod_ativo, tipo, data, descricao) values ('" . $cod_ativo . "', '". $tipo . "', '". $data . "', '" . $descricao . "');";
              fwrite($handle_hist, utf8_encode($sql) . "\n");
              
            }

          }
          
          if (strpos($linha, "</tbody>") !== false){
            $body = false;
            continue;
          }

        }
        
      }
      
      fclose($handle);
      
    }
    
    fclose($handle_hist);
    echo "Finalizado!";
    
    exit;
    
  }


  
  public function importarProventos() {

    $preco_dao = new \app\model\PrecoDAO();
    $lista_ativos = $preco_dao->listarAtivos();
    
    foreach ($lista_ativos as $ativo) {
      $cod_ativo = $ativo["cod_ativo"];

      $destino = CAMINHO_SISTEMA . "/cotahist/proventos/" . $cod_ativo . ".html";
      $url_fonte = "http://www.guiainvest.com.br/provento/default.aspx?sigla=" . $cod_ativo;
      if (file_exists($destino)) {
        continue;
      }

      $handle_importacao = fopen($destino, "w+");
      if (!$handle_importacao) {
        echo "Erro ao criar o arquivo de destino";
        exit;
      }

      $handle_fonte = @fopen($url_fonte, "r");
      if ($handle_fonte) {
        $gravando = false;
        while (($buffer = fgets($handle_fonte, 4096)) !== false) {
          if (trim($buffer) == '<div id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao" class="RadGrid RadGrid_Padrao">') {
            $gravando = true;
          }
          if ($gravando) {
            $buffer = str_replace('<table cellspacing="0" class="rgMasterTable" border="0" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00" style="width:100%;table-layout:auto;empty-cells:show;">', '<table>', $buffer);
            $buffer = str_replace('<colgroup>', '', $buffer);
            $buffer = str_replace('<col  />', '', $buffer);
            $buffer = str_replace('</colgroup>', '', $buffer);

            $buffer = str_replace('<tr class="rgRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__0">', '<tr>', $buffer);
            $buffer = str_replace('<tr class="rgRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__1">', '<tr>', $buffer);
            $buffer = str_replace('<tr class="rgRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__2">', '<tr>', $buffer);
            $buffer = str_replace('<tr class="rgRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__3">', '<tr>', $buffer);
            $buffer = str_replace('<tr class="rgRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__4">', '<tr>', $buffer);
            $buffer = str_replace('<tr class="rgRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__5">', '<tr>', $buffer);
            $buffer = str_replace('<tr class="rgRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__6">', '<tr>', $buffer);
            $buffer = str_replace('<tr class="rgRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__7">', '<tr>', $buffer);
            $buffer = str_replace('<tr class="rgRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__8">', '<tr>', $buffer);
            $buffer = str_replace('<tr class="rgRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__9">', '<tr>', $buffer);
            $buffer = str_replace('<tr class="rgRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__10">', '<tr>', $buffer);
            
            $buffer = str_replace('<tr class="rgAltRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__1">', '<tr>', $buffer);
            $buffer = str_replace('<tr class="rgAltRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__2">', '<tr>', $buffer);
            $buffer = str_replace('<tr class="rgAltRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__3">', '<tr>', $buffer);
            $buffer = str_replace('<tr class="rgAltRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__4">', '<tr>', $buffer);
            $buffer = str_replace('<tr class="rgAltRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__5">', '<tr>', $buffer);
            $buffer = str_replace('<tr class="rgAltRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__6">', '<tr>', $buffer);
            $buffer = str_replace('<tr class="rgAltRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__7">', '<tr>', $buffer);
            $buffer = str_replace('<tr class="rgAltRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__8">', '<tr>', $buffer);
            $buffer = str_replace('<tr class="rgAltRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__9">', '<tr>', $buffer);
            $buffer = str_replace('<tr class="rgAltRow" id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ctl00__10">', '<tr>', $buffer);
            
            


            $buffer = str_replace('<th scope="col" class="rgHeader" style="white-space:nowrap;text-align:center;">', '<th>', $buffer);
            $buffer = str_replace('<th scope="col" class="rgHeader">', '<th>', $buffer);
            $buffer = str_replace('<th scope="col" class="rgHeader tdMobile320" style="text-align:center;">', '<th>', $buffer);
            $buffer = str_replace('<td align="center" style="white-space:nowrap;">', '<td>', $buffer);
            $buffer = str_replace('<td class="tdMobile320" align="center" style="white-space:nowrap;">', '<td>', $buffer);

            $buffer = str_replace('<input id="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ClientState" name="ctl00_ctl00_cphConteudo_cphConteudo_ProventoAcao1_RadGridProventoAcao_ClientState" type="hidden" />', '', $buffer);

            fwrite($handle_importacao, $buffer);
          }


          if (trim($buffer) == '</div>' && $gravando) {
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

}
