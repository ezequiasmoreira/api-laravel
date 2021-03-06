<?php
namespace App\Http\Dto;
use App\Pais;

class PaisDTO
{
    public function __construct()  {;
    }
    public function obterPais($pais){
        
        $dto =[
            'id'        => $pais->id,
            'nome'      => $pais->nome,
            'codigo'    => $pais->codigo,
            'sigla'     =>  $pais->sigla 
        ];
        return $dto;
    }
    public function obterPaisTemplate($pais,$template=null){
        
        $dto = array();
        isset($template['pais.id'])     ? $dto = $dto  +   ['id'    => $pais->id]       : true;
        isset($template['pais.nome'])   ? $dto = $dto  +   ['nome'  => $pais->nome]     : true;
        isset($template['pais.codigo']) ? $dto = $dto  +   ['codigo' => $pais->codigo]  : true;
        isset($template['pais.sigla'])  ? $dto = $dto  +   ['sigla' => $pais->sigla]    : true;
        return $dto;
    }
}