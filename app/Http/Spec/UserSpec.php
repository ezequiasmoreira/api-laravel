<?php
namespace App\Http\Spec;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Validator;
use App\Http\Service\EmpresaService;
use App\Enums\Perfil;
use App\Http\Service\FuncionarioService;
use App\Http\Service\UserService;
use App\User;

class UserSpec
{
    private $empresaService;
    private $usuarioService;
    private $funcionarioService;
    public function __construct()  {
    }
    
    public function validarUsuario($usuario)
    {
        if(!$usuario){
            ApiException::throwException(5,'Usuário'); 
        }        
    }

    public function permiteSalvarInteresse(User $usuario)
    {
        return (count($usuario->interesses) > 0) ? ApiException::throwException(1) : true;  
    }

    public function validarPermissaoPorPerfil(User $usuario,User $usuarioLogado)
    {        
        $perfilUsuario = Perfil::getValue('Usuario');           
        if ($usuarioLogado->perfil == $perfilUsuario) {
           $this->validarPerfilUsuario($usuario,$usuarioLogado);
        }else{
            $this->validarPerfilFuncionario($usuario,$usuarioLogado);
            $this->validarVinculoFuncionario($usuario,$usuarioLogado);
        } 
        return true;
    }

    public function validarPerfilUsuario(User $usuario,User $usuarioLogado)
    {
        if($usuario->id != $usuarioLogado->id){
            ApiException::throwException(7,'('.$usuarioLogado->name.'),('.$usuario->name.')');
        }
    }
    
    public function usuarioLogadoPossuiEmpresa(){
        $this->usuarioService = new UserService();
        $this->empresaService = new EmpresaService();
        $usuarioLogado = $this->usuarioService->obterUsuarioLogado();
        $empresa = $this->empresaService->obterEmpresaPorUsuario($usuarioLogado);       
        return  ($empresa)?true:false;               
    }

    public function validarPermiteExcluirUsuario($usuario,$origem)
    {
        $this->usuarioService = new UserService();

        (Boolean)$ehFuncionario             = $this->ehFuncionario($usuario);
        (Boolean)$permiteSalvarFuncionario  = $this->permiteSalvarFuncionario($usuario); 
        (Boolean)$origemPermitida  = (($origem == 'Funcionario') || ($origem == 'Empresa'));        

        (!$permiteSalvarFuncionario && $ehFuncionario)  ? ApiException::throwException(30) : true;        
        ($ehFuncionario && ($origemPermitida==false))  ? ApiException::throwException(27) : true;
        
        if(!$ehFuncionario){
            $usuarioLogado = $this->usuarioService->obterUsuarioLogado();
            ($usuario->id != $usuarioLogado->id) ? ApiException::throwException(30) : true;
        }       
        return  true;               
    }

    public function validarPermiteExcluirUsuarioPorOrigem(User $usuario,$origem){
        $this->empresaService = new EmpresaService();

        if(($origem == "Empresa")){
            return  true;  
        } 

        (Boolean)$possuiEmpresa = $usuario->empresa;
        ($possuiEmpresa) ?  ApiException::throwException(20,$usuario->name) : true;        
        return true;                        
    }

    public function usuarioPossuiEmpresa($usuario){
        $this->empresaService = new EmpresaService();
        $empresa = $this->empresaService->obterEmpresaPorUsuario($usuario);
        if($empresa){
            return true;
        }
        return false;        
    }

    public function validarEmpresaVinculadaUsuarioLogado($empresa,$usuarioLogado)
    {
        if($empresa->usuario_id != $usuarioLogado->id){
            ApiException::throwException(10,$usuarioLogado->Name.','.$empresa->razao_social);
        }
        return true;
    }

    public function validarPerfilFuncionario($usuario,$usuarioLogado)
    {       
        $perfilFuncionario = Perfil::getValue('Funcionario');
        if (!($usuarioLogado->perfil == $perfilFuncionario)){
            ApiException::throwException(9,$usuarioLogado->perfil);
        } 
        if (!($usuario->perfil == $perfilFuncionario)){      
            ApiException::throwException(9,$usuario->perfil);
        } 
        return true;
    }

    public function permiteSalvarFuncionario($usuario)
    {
        $this->usuarioService = new UserService();
        $this->funcionarioService = new FuncionarioService();
        
        $usuarioLogado = $this->usuarioService->obterUsuarioLogado();
        if(!$this->ehFuncionario($usuario)){
            return false;
        }
        if(!$this->ehFuncionario($usuarioLogado)){
            return false;
        }
        if(!$this->usuarioLogadoPossuiEmpresa()){
            return false;
        }
        $funcionario = $this->funcionarioService->obterFuncionarioPorUsuario($usuarioLogado);  
        if(!$funcionario){
            return false;
        } 
        $empresa = $funcionario->empresa;
        if($empresa->usuario_id != $usuarioLogado->id){
            return false;
        }   
        return true;
    }

    private function ehFuncionario($usuario)
    {
        $perfilFuncionario = Perfil::getValue('Funcionario');
        if (!($usuario->perfil == $perfilFuncionario)){
            return false;
        }         
        return true;
    }

    public function permitePerfilFuncionario($perfil,$permite)
    {
        $perfilFuncionario = Perfil::getValue('Funcionario');
        if($perfil != $perfilFuncionario){
            return true;
        }
        if(!$permite){            
            ApiException::throwException(12);
        }
        return true;
    }

    public function validarPerfilPermitido($perfil,$perfisPermitido)
    {
        $permitido = false;
        $perfis ='';
        foreach ($perfisPermitido as $perfilPermitido) { 
            $perfis =  ($perfis != '' )? $perfis.' - '.$perfilPermitido: $perfis.$perfilPermitido;
            if($perfilPermitido == $perfil){
                $permitido = true;
            }
        } 
        if(!$permitido){
            ApiException::throwException(8,$perfil.','.$perfis);
        }
        return true;      
    }

    public function validarCamposObrigatorioSalvar($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            ]);
            if($validator->fails()){
                ApiException::throwException(11,$validator->errors()->toJson());
            }
        return true;      
    }

    public function validarCamposObrigatorioAtualizar($request)
    {        
        $validator = Validator::make($request->all(), [
            'id' =>  'required',
            'name' => 'required|string|max:255'
        ]);
        if($validator->fails()){
            ApiException::throwException(11,$validator->errors()->toJson());
        }
        return true;      
    }

    public function validarStatus($enviado,$esperado,$mensagemDoErro,$parametros=null)
    {        
        if($enviado != $esperado){
            ApiException::throwException(16,$mensagemDoErro,$parametros);
        }
        return true;      
    }
}
