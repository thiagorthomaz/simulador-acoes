<?php

namespace app\model;

/**
 * Description of Tab_hist_proventosDAO
 *
 * @author thiago
 */
class Tab_hist_proventosDAO extends \app\model\DAO {
  
  public function getModel() {
    return new \app\model\Tab_hist_proventos();
  }

  public function getTable() {
    return "Tab_hist_provento";
  }

  public function getProventos(){

    $sql = "select cod_ativo, data, descricao, tipo  from " . $this->getTable();
    $sql .= " where ";
    $sql .= " tipo in ('Grupamento', 'Desdobramento') and data >= '2005-01-01' and atualizado = false";
    $sql .= " order by 1,2,3,4 asc";

    $rs = $this->sendQuery($sql);
    return $rs->getResultSet();    
    
  }
  
  public function modeltoArray(\stphp\Database\iDataModel $data_model) {
    
  }

}
