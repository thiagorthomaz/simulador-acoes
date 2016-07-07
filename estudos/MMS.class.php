<?php

namespace app\estudos;

/**
 * @author Toloi
 */
class MMS {

    protected $candles;
    protected $dados = array();
    protected $periodos;

    public function __construct($dados, $periodos) {

        if (is_array($dados) && $periodos > 1) {
            $this->candles = $dados;
            $this->periodos = $periodos;
        } else {
            throw new Exception("Dados inconsistentes passados para o construtor da classe 'MediaMovelSimples'");
        }
    }

    /**
     * Enter description here...
     *
     * @param int $periodo
     */
    public function calcula() {
        //Fórmula -> SMA = [Fech(x) + Fech(x-1) + Fech(x-2) + … + Fech(x-9)] ÷ 10

        if ($this->periodos < 1) {
            throw new Exception("Período inválido para a Média Móvel Simples.");
        }

        for ($i = 0; $i < count($this->candles); $i++) {
            $this->dados[$i] = 0;
            $soma = 0;
            if ($i + 1 >= $this->periodos) {
                for ($j = $i; $j >= (($i + 1) - $this->periodos); $j--) {
                    $soma += $this->candles[$j]->getFechamento();
                }
                $media = $soma / $this->periodos;
                $this->dados[$i] = $media;
            }
        }

    }
    
    public function getPeriodos() {
       return $this->periodos;
    }

    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getResultado() {
        return $this->dados;
    }

}

?>