<?php
namespace App\Http\Spec;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\ApiException;
use App\Http\Service\EmpresaService;
use App\Http\Service\FuncionarioService;

class EnderecoSpec
{
    private $empresaService;
    private $funcionarioService;
    public function __construct()  {
    }
    
    public function validarCamposObrigatorioSalvar($request){  
        $validator = Validator::make($request->all(), [
            'rua' => 'required|string|max:255',
            'bairro' => 'required|string|max:255',
            'numero' => 'required',
            'cep' => 'required|string|max:9|min:9',
            'cidade_id' => 'required',
        ]);
        if($validator->fails()){
            ApiException::throwException(11,$validator->errors()->toJson());
        }
        return true;    
    }
    public function validar($endereco){ 
        $this->existeEndereco($endereco);
        return true;    
    }
    private function existeEndereco($endereco){ 
        if(!$endereco){
            ApiException::throwException(5,'Endereço');
        }
        return true;    
    }
}
