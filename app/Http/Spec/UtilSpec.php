<?php
namespace App\Http\Spec;
use App\Exceptions\ApiException;
class UtilSpec
{
    public function __construct()  {
    }
    public function validarCnpj($cnpj){ 
        $this->existeCnpj($cnpj);
        $this->cnpjQuantidadeDigitosValidoComPonto($cnpj);
        $this->cnpjQuantidadeDigitosValidoSemPonto($cnpj);
        if(!$this->cnpjValido($cnpj)){
            ApiException::lancarExcessao(5,'CNPJ: '.$cnpj);
        }
        return true;      
    }
    private function existeCnpj($cnpj){          
        if(!$cnpj){
            ApiException::lancarExcessao(14,'CNPJ');
        }
        return true; 
    }
    private function cnpjQuantidadeDigitosValidoComPonto($cnpj){ 
        $quantidadeCorreta = 18;         
        if(strlen($cnpj)!=$quantidadeCorreta){
            ApiException::lancarExcessao(15,'CNPJ'.','.$quantidadeCorreta);
        }
        return true; 
    }
    private function cnpjQuantidadeDigitosValidoSemPonto($cnpj){ 
        $quantidadeCorreta = 14;   
        $cnpj =  str_replace('.','',$cnpj);     
        $cnpj =  str_replace('/','',$cnpj);     
        $cnpj =  str_replace('-','',$cnpj);     
        if(strlen($cnpj)!=$quantidadeCorreta){
            ApiException::lancarExcessao(16,'CNPJ'.','.'XX.XXX.XXX/XXXX-XX');
        }
        return true; 
    }
    private function cnpjValido($cnpj){
        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);        
        // Valida tamanho
        if (strlen($cnpj) != 14){
            return false;
        }
        // Verifica se todos os digitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj)){
            return false;
        }
        // Valida primeiro dígito verificador
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++){
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)){
            return false;
        }
        // Valida segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++){
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }
    public function validarStatus($enviado,$esperado,$mensagemDoErro){        
        if($enviado != $esperado){
            ApiException::lancarExcessao(16,$mensagemDoErro);
        }
        return true;      
    }
}
    

