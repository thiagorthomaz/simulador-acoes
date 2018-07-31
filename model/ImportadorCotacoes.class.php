<?php

namespace app\model;

class ImportadorCotacoes {

   /**
    * Caminho default onde o importador procurará por arquivos de cotações
    *
    * @var string
    */
   private $path;

   /**
    * Handle de manipulação do arquivo de log de erros.
    *
    * @var resource
    */
   private $logfile_handle;

   /**
    *
    * @var \app\model\DAO
    */
   private $conexao_db;
   
   public function __construct(\stphp\Database\Connection $db_conn, $data) {
     
      $this->path = "./";
      $this->conexao_db = $db_conn;

      $this->logfile_handle = fopen(CAMINHO_SISTEMA . "/log/log_erros_" . $data . ".txt", "a");
      
   }

   public function __destruct() {
      fclose($this->logfile_handle);
   }

   /**
    * Enter description here...
    *
    * @param array $dados
    */
   public function gravaDados($dados) {
     
     $sql = "INSERT INTO Tab_preco (data_pregao, cod_ativo, abertura, maxima, minima, medio, fechamento, negocios, volume_financeiro, data_importacao) "
         . "VALUE (:data_pregao, :cod_ativo, :abertura, :maxima, :minima, :medio, :fechamento, :negocios, :volume_financeiro, :data_importacao)";
      $rs = $this->conexao_db->sendQuery($sql, $dados);
      if (!is_null($rs->getError_code())) {
        $msg = $rs->getError_message();
         throw new \Exception($msg);
      }
   }

   /**
    * Enter description here...
    *
    */
   public function importaDiretorio() {
      $handle = opendir($this->path);

      while (false !== ($file = readdir($handle))) {
         if ($file != "." && $file != "..") {
            $this->importaArquivo($file);
         }
      }
      closedir($handle);
   }

   /**
    * Enter description here...
    *
    * @param string $absolute_path
    */
   public function setPath($absolute_path) {
      $this->path = $absolute_path;
   }

   /**
    * Enter description here...
    *
    * @param string $filename
    * @return array
    */
   public function leArquivo($filename) {
      return file($filename);
   }

   /**
    * Enter description here...
    *
    * @param string $filename
    */
   public function importaArquivo($filename) {

      $arquivo = $this->leArquivo($filename);

      //Verifica se é um arquivo válido BOVESPA, ou seja,
      //nas primeiras posições do arquivo deve existir:
      //"00COTAHIST." (arquivo anual) ou "00BDIN9999BOVESPA 9999" (arquivo diário) 
      $header = $arquivo[0];
      if (substr($header, 0, 11) != '00COTAHIST.' && substr($header, 0, 22) != '00BDIN9999BOVESPA 9999') {
         throw new \Exception("Arquivo de cotações da BOVESPA inválido.");
      }

      //Chama a função específica para o arquivo em questão.
      if (substr($header, 0, 11) == '00COTAHIST.') {

         for ($i = 1; $i < count($arquivo); $i++) {
            $precos = $this->capturaPrecosHistoricoBovespa($arquivo[$i]);
            if (count($precos) > 0) {
               try {
                  $this->gravaDados($precos);
               } 
               catch (\Exception $e) {
               $this->logaErroImportacao($precos, $e->getMessage());
               }
            }
         }
      } 
      else {
         for ($i = 1; $i < count($arquivo); $i++) {
            $precos = $this->capturaPrecosDiarioBovespa($header, $arquivo[$i]);
            if (count($precos) > 0) {
               try {
                  $this->gravaDados($precos);
               } 
               catch (\Exception $e) {
                  $this->logaErroImportacao($precos, $e->getMessage());
               }
            }
         }
      }
   }

   /**
    * Enter description here...
    *
    * @param array $arquivo
    * @return array
    */
   public function capturaPrecosHistoricoBovespa($linha) {

      //Array que armazenará todos os preços do arquivo.
      $campos = array();

      /**
       * Tipos de negociações que serão importadas:
       * 01 VISTA - FUTURO VISTA P/ LIQ. MERCADO FUTURO
       * 02 LOTE PADRAO
       * 06 CONCORDATARIAS
       * 08 RECUP. JUDICIALRECUPERACAO JUDICIAL/EXTRAJUDICIAL
       * 10 DIREITO/RECIBO DIREITOS E RECIBOS
       * 12 FDO IMOBILIARIOFUNDOS IMOBILIARIOS
       * 14 CERT.INVEST/TIT.DIV.PUBLICA
       * 16 OPERACAO ESTRUTURADA
       * 18 OBRIGACOES
       * 22 BONUS (PRIVADOS)
       */
      $lista_tipos_negoc = array(1, 2, 6, 8, 10, 12, 14, 16, 18, 22);

      //Posição 1 a 2 - Tipo do registro
      $tipo_registro = intval(substr($linha, 0, 2));

      //Posição 11 a 13 - Código BD
      $tipo_negociacao = intval(substr($linha, 10, 2));

      //Verifica se o registro é do tipo "02 - RESUMO DIÁRIO DE NEGOCIAÇÕES POR PAPEL" e s
      //o código do tipo de negociação está na lista de tipos aceitos pelo importador.
      if ($tipo_registro == 1 && in_array($tipo_negociacao, $lista_tipos_negoc)) {

         $campos['data_pregao'] = substr($linha, 2, 4) . "-" . substr($linha, 6, 2) . "-" . substr($linha, 8, 2);

         //Posição 13 a 24 - Código do papel
         $campos['cod_ativo'] = trim(substr($linha, 12, 12));

         //Posição 57 a 69 - Preço de abertura
         $campos['abertura'] = floatval(substr($linha, 56, 13)) / 100;

         //Posição 70 a 82 - Preço máximo do dia
         $campos['maxima'] = floatval(substr($linha, 69, 13)) / 100;

         //Posição 83 a 95 - Preço mínimo do dia
         $campos['minima'] = floatval(substr($linha, 82, 13)) / 100;

         //Posição 96 a 108 - Preço médio
         $campos['medio'] = floatval(substr($linha, 95, 13)) / 100;

         //Posição 109 a 121 - Preço de fechamento
         $campos['fechamento'] = floatval(substr($linha, 108, 13)) / 100;

         //Posição 148 a 152 - Número de negócios
         $campos['negocios'] = intval(substr($linha, 147, 5));

         //Posição 171 a 188 - Volume financeiro
         $campos['volume_financeiro'] = floatval(substr($linha, 170, 18)) / 100;

         //Data do atual processamento.
         $campos['data_importacao'] = date("Y-m-d H:i:s");
      }

      return $campos;
   }

   /**
    * Método utilizado para importar arquivos diários fornecidos pelo site da Bovespa.
    *
    * @param array $arquivo
    * @return array
    */
   public function capturaPrecosDiarioBovespa($header, $linha) {

      //Array que armazenará todos os preços.
      $campos = array();

      //Captura a data do pregão - Posição 31 a 38 do Header (AAAA-MM-DD)
      $data_pregao = substr($header, 22, 4) . "-" . substr($header, 26, 2) . "-" . substr($header, 28, 2);

      /**
       * Tipos de negociações que serão importadas:
       * 01 VISTA - FUTURO VISTA P/ LIQ. MERCADO FUTURO
       * 02 LOTE PADRAO
       * 06 CONCORDATARIAS
       * 08 RECUP. JUDICIALRECUPERACAO JUDICIAL/EXTRAJUDICIAL
       * 10 DIREITO/RECIBO DIREITOS E RECIBOS
       * 12 FDO IMOBILIARIOFUNDOS IMOBILIARIOS
       * 14 CERT.INVEST/TIT.DIV.PUBLICA
       * 16 OPERACAO ESTRUTURADA
       * 18 OBRIGACOES
       * 22 BONUS (PRIVADOS)
       */
      $lista_tipos_negoc = array(1, 2, 6, 8, 10, 12, 14, 16, 18, 22);

      //Posição 1 a 2 - Tipo do registro
      $tipo_registro = intval(substr($linha, 0, 2));

      //Posição 3 a 5 - Código BD
      $tipo_negociacao = intval(substr($linha, 2, 2));

      //Verifica se o registro é do tipo "02 - RESUMO DIÁRIO DE NEGOCIAÇÕES POR PAPEL" e s
      //o código do tipo de negociação está na lista de tipos aceitos pelo importador.
      if ($tipo_registro == 2 && in_array($tipo_negociacao, $lista_tipos_negoc)) {

         $campos['data_pregao'] = $data_pregao;

         //Posição 58 a 69 - Código do papel
         $campos['cod_ativo'] = trim(substr($linha, 57, 12));

         //Posição 91 a 101 - Preço de abertura
         $campos['abertura'] = floatval(substr($linha, 90, 11)) / 100;

         //Posição 102 a 112 - Preço máximo do dia
         $campos['maxima'] = floatval(substr($linha, 101, 11)) / 100;

         //Posição 113 a 123 - Preço mínimo do dia
         $campos['minima'] = floatval(substr($linha, 112, 11)) / 100;

         //Posição 124 a 134 - Preço médio
         $campos['medio'] = floatval(substr($linha, 123, 11)) / 100;

         //Posição 135 a 145 - Preço de fechamento
         $campos['fechamento'] = floatval(substr($linha, 134, 11)) / 100;

         //Posição 174 a 178 - Total de negócios do papel
         $campos['negocios'] = intval(substr($linha, 173, 5));

         //Posição 194 a 210 - Volume financeiro
         $campos['volume_financ'] = floatval(substr($linha, 193, 17)) / 100;

         //Data do atual processamento.
         $campos['data_importacao'] = date("Y-m-d H:i:s");
      }

      //No final do método, retorna a lista dos preços.
      return $campos;
   }

   /**
    * Grava log em arquivo os erros de importação.
    *
    * @param array $dados_cotacao
    * @param string $mensagem_erro
    */
   private function logaErroImportacao($dados_cotacao, $mensagem_erro) {
      try {
         $erro = date("Y-m-d H:i:s") . " - Erro ao importar a cotação: " . implode(";", $dados_cotacao) . ". Errormsg: " . $mensagem_erro . "\n";
         fwrite($this->logfile_handle, $erro);
      } catch (\Exception $e) {
         echo "falha no log;";
      }
   }

}

?>