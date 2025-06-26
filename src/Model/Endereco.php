<?php

class Endereco
{
    private string $cep;
    private string $cidade;
    private string $estado;
    private string $logradouro;
    private string $bairro;
    private string $numero;

    public function __construct(string $cep, string $cidade, string $estado, string $logradouro, string $bairro, string $numero)
    {
        $cepValidado = $this->validaCEP($cep);
        
        if ($cepValidado === false) {
            header("Location: index.php?erro=CEP");
            die();
        }
        $this->cep = $this->limpaCep($cep);
        $this->cidade = $cidade;
        $this->estado = $estado;
        $this->logradouro = $logradouro;
        $this->bairro = $bairro;
        $this->numero = $numero;
    }

    public function recuperaCep(): string
    {
        return $this->cep;
    }

    public function recuperaCidade(): string
    {
        return $this->cidade;
    }

    public function recuperaEstado(): string
    {
        return $this->estado;
    }

    public function recuperaLogradouro(): string
    {
        return $this->logradouro;
    }

    public function recuperaBairro(): string
    {
        return $this->bairro;
    }

    public function recuperaNumero(): string
    {
        return $this->numero;
    }

    private function validaCEP(string $cep): bool
    {
        return preg_match("/^[0-9]{5}\-[0-9]{3}$/", $cep);
    }

    private function limpaCep(string $cep): string
    {
        return str_replace("-","", $cep);
    }
}