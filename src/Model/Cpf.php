<?php

class Cpf
{
    private string $cpf;
    private const PESO_10 = 10;
    private const PESO_11 = 11;
    public function __construct(string $cpf)
    {
        $cpfValidado = $this->validaFormatacao($cpf);
        $validacaoDigitosVerificadores = $this->validaDigitosVericadores($cpf);
        if($cpfValidado === false || $validacaoDigitosVerificadores === false)
        {
            header("Location: index.php?erro=CPF");
            die();
        }

        $this->cpf = $this->limpaCPF($cpf);
    }

    public function recuperaCpf(): string
    {
        return $this->cpf;
    }

    private function validaFormatacao(string $cpf): bool
    {
        return preg_match("/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}$/", $cpf);
    }

    private function limpaCPF(string $cpf): string
    {
        return str_replace([".","-"], "", $cpf);
    }

    private function calculaDigitoVerificador(string $numeroCpf, int $pesoMultiplicadores):string
    {
        // Prepara o cpf e transforma ele em Array para realizar as multiplicações
        $cpfArray = str_split($numeroCpf);
        $tamanhoCpf = count($cpfArray);
        // 1º Passo: Realizar a multiplicação dos dígitos do CPF e de uma sequência de pesos associados a cada um deles. O resultado de cada multiplicação é somado:
        for ($i = 0; $i < $tamanhoCpf; $i++){
            $resultadoMultiplicacao[$i]  = $cpfArray[$i] * $pesoMultiplicadores;
            $pesoMultiplicadores--;
        }
        $somaDoCpf = array_sum($resultadoMultiplicacao);
        // 2º Passo: O resultado da soma das multiplicações é dividido por 11, com o propósito de obter o resto da divisão:
        $restoDaDivisao = $somaDoCpf % 11;
        // 3º Passo:  Se o resto da divisão for menor que 2, logo o primeiro dígito verificador é 0; caso contrário, o primeiro dígito verificador é obtido através da subtração de 11 menos o resto da divisão;
        if ($restoDaDivisao < 2){
            return 0;
        }
        $resultadoSubtracao = 11 - $restoDaDivisao;
        return $resultadoSubtracao;
    }

    private function validaDigitosVericadores($cpf):bool
    {
        // Preparando o CPF com 9 dígitos
        $cpfSemFormatacao = $this->limpaCpf($cpf);
        $cpfCom9PrimeirosDigitos = substr($cpfSemFormatacao,0,9);
        // 1º Passo:Calculando o primeiro dígito verificador
        $primeiroDigitoVerificador = $this->calculaDigitoVerificador($cpfCom9PrimeirosDigitos, self::PESO_10);
        // 2º Passo: Calculando o segundo dígito verificador
        $cpfCom10PrimeirosDigitos = $cpfCom9PrimeirosDigitos . $primeiroDigitoVerificador;
        $segundoDigitoVerificador = $this->calculaDigitoVerificador($cpfCom10PrimeirosDigitos, self::PESO_11);
        /* 3º Passo: Comprar se os 2 dígitos verificadores encontrados são iguais
         aos dígitos verificadores do CPF analisado. Se forem iguais, então o CPF é válido.*/
        $cpfAposValidacao = $cpfCom9PrimeirosDigitos . $primeiroDigitoVerificador . $segundoDigitoVerificador;
        if ($cpfSemFormatacao != $cpfAposValidacao){
            return false;
        }
        return true;
    }
}