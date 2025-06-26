<?php

class DataNascimento
{
    private string $data;
    public function __construct(string $data)
    {
        $dataValidada = $this->validaData($data);
        $numerosValidados = $this->validaNumeros($data);
        if ($dataValidada === false || $numerosValidados === false) {
            header("Location:index.php?erro=Data de Nascimento");
            die();
        }
        if($this->verificaDataFutura($data) === true || $this->verificaDataMenosCincoAnos( $data) === true) {
            header("Location: index.php?erro=Data de Nascimento");
            die();
        }
        $this->data = $data;
    }

    public function recuperaDataCompleta(): string
    {
        return $this->data;
    }

    private function validaData(string $data): bool
    {
        return preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/", $data);
    }

    private function validaNumeros(string $data): bool
    {
        $dataSeparada = explode("-", $data);
        $ano = $dataSeparada[0];
        $mes = $dataSeparada[1];
        $dia = $dataSeparada[2];
        return checkdate($mes, $dia, $ano);

    }

    private function verificaDataFutura(string $data): bool
    {
        $dataAtual = new DateTimeImmutable();
        return $dataAtual < $data;

    }

    private function verificaDataMenosCincoAnos(string $data): bool
    {
        $dataAtual = new DateTimeImmutable();
        $dataAtual->sub(new DateInterval("P5Y"));
        return $dataAtual < $data;
    }

}