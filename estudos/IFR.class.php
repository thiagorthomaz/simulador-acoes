<?php

namespace app\estudos;

/**
 * @author Toloi
 */
class IFR {

    private $dados;

    public function __construct($dados) {
        $this->dados = $dados;
    }

    /**
     * Enter description here...
     *
     * @param int $periodo
     */
    public function calcula($periodo) {

        if ($periodo < 2) {
            throw new Exception("Período inválido para o Índice de Força Relativa (IFR).");
        }

        //O primeiro IFR sempre será 50, pois não existem dados anteriores à ele.
        //$this->dados[0]['ifr'] = 50;

        $primeiro_ifr = true;

        for ($i = $periodo; $i < count($this->dados); $i++) {

            $M_positivo = 0;
            $M_negativo = 0;

            //Coleta os valores do período
            for ($j = ($i - $periodo) + 1; $j <= $i; $j++) {

                //Verifica se o fechamento do período é maior ou menor que o anterior  
                if ($this->dados[$j]['fechamento'] > $this->dados[$j - 1]['fechamento']) {
                    $M_positivo += $this->dados[$j]['fechamento'] - $this->dados[$j - 1]['fechamento'];
                }
                if ($this->dados[$j]['fechamento'] < $this->dados[$j - 1]['fechamento']) {
                    $M_negativo += $this->dados[$j - 1]['fechamento'] - $this->dados[$j]['fechamento'];
                }
            }

            if ($primeiro_ifr) {
                $Mup = $M_positivo / $periodo;
                $Mdown = $M_negativo / $periodo;
                $primeiro_ifr = false;
            } else {
                $Mup = (($Mup_anterior * ($periodo - 1)) + $M_positivo) / $periodo;
                $Mdown = (($Mdown_anterior * ($periodo - 1)) + $M_negativo) / $periodo;
            }

            if ($Mdown == 0) {
                $ifr = 100;
            } else {
                $ifr = 100 - (100 / (1 + ($Mup / $Mdown)));
            }

            $Mup_anterior = $Mup;
            $Mdown_anterior = $Mdown;
            $this->dados[$i]['ifr'] = $ifr;
        }
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getResultado() {
        return $this->dados;
    }

}

