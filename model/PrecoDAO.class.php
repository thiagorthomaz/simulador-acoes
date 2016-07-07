<?php

namespace app\model;

/**
 * Description of PrecoDAO
 *
 * @author thiago
 */
class PrecoDAO extends \app\model\DAO {
  
  
  public function getModel() {
    return new Preco();
  }

  public function getTable() {
    return "Tab_preco";
  }

  public function precaoAtivo($codigo_ativo){

    $params = array('cod_ativo' => $codigo_ativo);
    
    $sql = "select * from " . $this->getTable();
    $sql .= $this->where($params);
    $rs = $this->sendQuery($sql, $params);
    return $rs->getResultSet();
    
  }
  
  
  public function modeltoArray(\stphp\Database\iDataModel $data_model) {
    
  }

}
