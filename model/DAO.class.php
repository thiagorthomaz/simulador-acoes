<?php

namespace app\model;

/**
 * Description of DAO
 *
 * @author thiago
 */
abstract class DAO extends \stphp\Database\MySQL {
  
public function __construct() {
  
    $pdo_config = new \app\config\PDOConfig();
    $pdo_config->setUser("root");
    $pdo_config->setpassword("");
    
    
    $this->connect($pdo_config);
  }
  
  
}
