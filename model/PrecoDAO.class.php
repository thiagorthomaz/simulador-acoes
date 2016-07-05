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

  public function modeltoArray(\stphp\Database\iDataModel $data_model) {
    
  }

}
