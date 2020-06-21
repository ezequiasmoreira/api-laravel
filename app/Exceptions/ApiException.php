<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\ExceptionMensagem;

class ApiException extends Exception
{
    public static function lancarExcessao($mensagemCodigo,$codigos=''){
        $mensagem = ExceptionMensagem::obterMensagem($mensagemCodigo);
        $cont = (Integer) 0;
        $retorno = (String) '';
        $campos = explode("?", $mensagem);
        $parametros = $mensagem != '?' ? explode(",", $codigos) : explode($codigos.'...', $codigos);
        
        if($codigos != ''){
           if (count($campos)-1 !=  count($parametros)){
                $retorno = 'Quantidade de parametros incorreto Quantidade de ?: '.(count($campos)-1).' Parametros: '.count($parametros).' Mensagem separada por virgula: '.$codigos;
            }else{
                foreach ($campos as $campo) {
                   if($cont < (count($campos)-1)){
                        $retorno = $retorno.$campo.($parametros[$cont] ? $parametros[$cont] : '');
                   }else{
                        $retorno = $retorno.$campo;
                   }
                   $cont = $cont +1;
                }
            }
        }else{
            $retorno =  str_replace('?','',$mensagem);
        }        
        throw new Exception($retorno );
    }
}
