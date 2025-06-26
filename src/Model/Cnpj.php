<?php
class Cnpj
{
    private string $cnpj;
    private const PESO_12 = [5,4,3,2,9,8,7,6,5,4,3,2];
    private const PESO_13 = [6,5,4,3,2,9,8,7,6,5,4,3,2];
    public function __construct(string $cnpj)
    {
        $formatacaoValida = $this->validaFormatacao($cnpj);
        $validacaoDigitosVerificadores = $this->validaDigitosVerificadores($cnpj);
        if ($validacaoDigitosVerificadores === false OR $formatacaoValida === false){
            header("Location: index.php?erro=CNPJ");
            die();
        }
        $this->cnpj = $this->limpaFormatacao($cnpj);
    }
    private function validaFormatacao(string $cnpj):bool
    {
        return preg_match("/^[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}\-[0-9]{2}$/", $cnpj);
    }
    private function limpaFormatacao(string $cnpj): string
    {
        return str_replace(['.','-','/'],"",$cnpj);
    }
    private function validaDigitosVerificadores(string $cnpj):bool
    {
        //Preparando o CNPJ com 12 dígitos
        $cnpjSemFormatacao = $this->limpaFormatacao($cnpj);
        $cnpjCom12PrimeirosDigitos = substr($cnpjSemFormatacao,0,12);
        // 1º Passo: Calculando o primeiro dígito verificador
        $primeiroDigitoVerificador = $this->calculaDigitoVerificador($cnpjCom12PrimeirosDigitos, self::PESO_12);
        // 2º Passo: Calculando o segundo dígito verificador
        $cnpjCom13PrimeirosDigitos = $cnpjCom12PrimeirosDigitos . $primeiroDigitoVerificador;
        $segundoDigitoVerificador = $this->calculaDigitoVerificador($cnpjCom13PrimeirosDigitos, self::PESO_13);
        /*3º Passo: Comparar se os 2 dígitos verificadores encontrados são iguais aos dígitos 
        verificadores do CNPJ analisado. Se forem iguais, então o CNPJ é válido.*/
        $cnpjAposValidacao = $cnpjCom12PrimeirosDigitos . $primeiroDigitoVerificador . $segundoDigitoVerificador;
        if ($cnpjSemFormatacao != $cnpjAposValidacao){
            return false;
        }
        return true;
    }
    private function calculaDigitoVerificador(string $numeroCnpj, array $pesoMultiplicadores):string
    {
        //Prepara o cnpj e transforma ele em Array para realizar as multiplicações
        $cnpjArray = str_split($numeroCnpj);
        $tamanhoCnpj = count($cnpjArray);
        //1º Passo: Realizar a multiplicação dos dígitos do CNPJ e de uma sequência de pesos associados a cada um deles. O resultado de cada multiplicação é somado:
        for ($i = 0; $i < $tamanhoCnpj; $i++){
            $resultadoMultiplicacao[$i]  = $cnpjArray[$i] * $pesoMultiplicadores[$i];
        }
        $somaDoCnpj = array_sum($resultadoMultiplicacao);
        // 2º Passo: O resultado da soma das multiplicações é dividido por 11, com o propósito de obter o resto da divisão:
        $restoDaDivisao = $somaDoCnpj % 11;
        //3º Passo:  Se o resto da divisão for menor que 2, logo o primeiro dígito verificador é 0; caso contrário, o primeiro dígito verificador é obtido através da subtração de 11 menos resto da divisão;
        if ($restoDaDivisao < 2){
            return 0;
        }
        $resultadoSubtracao = 11 - $restoDaDivisao;
        return $resultadoSubtracao;
    }
    public function recuperaNumero(): string
    {
        return $this->cnpj;
    }
}