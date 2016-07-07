<?php

namespace app\estudos;

/**
 * @author Toloi
 */
class MME extends MMS {

    public function calcula($periodo = 9) {

        //Fórmula -> MMEx = ME(x-1) + K x {Fech(x) – ME(x-1)} 
        //* MMEx representa a média móvel exponencial no dia x
        //* ME(x-1) representa a média móvel exponencial no dia x-1
        //* N é o número de dias para os quais se quer o cálculo
        //* Constante K = {2 ÷ (N+1)}

        if ($periodo < 1) {
            throw new Exception("Período inválido para a Média Móvel Exponencial.");
        }

        $K = 2 / ($periodo + 1);

        $this->MediaExponencial($periodo, count($this->dados) - 1, $K);
    }

    /**
     * Enter description here...
     *
     * @param int $posicao
     * @return float
     */
    private function MediaExponencial($periodo, $posicao, $K) {
        if ($posicao == 0) {
            $media = $this->dados[0]['fechamento'];
        } else {
            $media_anterior = $this->MediaExponencial($periodo, $posicao - 1, $K);
            $media = $media_anterior + ($K * ($this->dados[$posicao]['fechamento'] - $media_anterior));
        }
        $this->dados[$posicao]['media_exponencial'] = $media;
        return $media;
    }

}

?>