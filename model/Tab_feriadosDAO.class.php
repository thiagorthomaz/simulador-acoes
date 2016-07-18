<?php

namespace app\model;


/**
 * Description of Tab_feriadosDAO
 *
 * @author thiago
 */
class Tab_feriadosDAO extends \app\model\DAO{
  
  public function getModel() {
    return new \app\model\Tab_hist_proventos();
  }

  public function getTable() {
    return "Tab_feriados";
  }
  
  public function verificaSeFeriado($data){
    
    $sql = "select * from Tab_feriados where data = :data"; 
    $params = array("data" => date("Y-m-d", strtotime($data)));
    $rs = $this->sendQuery($sql, $params);
    return count($rs->getResultSet()) > 0;
  }

  public function modeltoArray(\stphp\Database\iDataModel $data_model) {
    
  }

}
