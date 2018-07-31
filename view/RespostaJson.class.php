<?php

namespace app\view;

/**
 * Description of RespostaJson
 *
 * @author thiago
 */
class RespostaJson extends \stphp\http\HttpResponse implements \JsonSerializable {
  
  
  public function output() {
    echo $this->jsonSerialize();
  }

  public function jsonSerialize() {
    return json_encode(array("conteudo" => $this->content));
  }

  public function addArray($index_name, $array){
    if (!is_array($array)){
      throw new \Exception("Se o conteúdo não for array, você deve usar um objeto que seja ArraySerializable");
    }
    
    $this->content[$index_name] = $array;
    
  }
  
  public function getStatus() {
    return 200;
  }

  public function getType() {
    return "json";
  }

  public function serialize() {
    
  }

}
