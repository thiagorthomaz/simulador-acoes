<?php

namespace app\model;

/**
 * Description of Calendario
 *
 * @author thiago
 */
class Calendario {
  
  
  public function getUltimoDiaUtil($data = null){
    if (is_null($data)){
      $data = date("Y-m-d h:i:s");
    }

    while ( $this->verificaSeFinalSemana($data) || $this->verificaSeFeriado($data)) {
      $dia_anterior = date("Y-m-d H:i:s", strtotime("-1 day", strtotime($data)));
      $data = $dia_anterior;  
    }
    return $data;   
  }
  
  public function verificaSeFinalSemana($data){
    $dia_semana = date('w', strtotime($data));
    return ($dia_semana == 0 || $dia_semana == 6);
  }
  
  
  function verificaSeFeriado($data) {
    $data = date("Y-m-d", strtotime($data));
    $dao = new \app\model\Tab_feriadosDAO();
    return $dao->verificaSeFeriado($data);
  }
      
  
  
}
