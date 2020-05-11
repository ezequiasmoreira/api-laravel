<?php
namespace App\Http\Controllers;
use App\Funcionario;
use App\Empresa;
use App\Endereco;
use App\User;
use App\Http\Service\EmpresaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class FuncionarioController extends Controller
{
    private $funcionario;
    public function __construct()  {
        $this->funcionario = new Funcionario();
    }

    public function salvar(Request $request){       
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'rua' => 'required|string|max:255',
            'numero' => 'required|integer',
            'bairro'  => 'required|string|max:255',
            'complemento'  => 'required|string|max:255',
            'cep'  => 'required|string|max:10',
            'cidade_id'  => 'required|integer',            
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $endereco = Endereco::create([
            'rua' => $request->get('rua'),
            'numero' => $request->get('numero'),
            'bairro' => $request->get('bairro'),
            'complemento' => $request->get('complemento'),
            'cep' => $request->get('cep'),
            'cidade_id' => $request->get('cidade_id'),
        ]);
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'perfil' => 'FUNCIONARIO',
            'endereco_id' => $endereco->id,
        ]);
        $empresa = new EmpresaService();
        $empresa = $empresa->obterEmpresaUsuarioLogado();
        
        $funcionario = Funcionario::create([
            'usuario_id' => $user->id,
            'endereco_id' => $endereco->id,
            'empresa_id' => $empresa->id,
        ]);        
        return response()->json($funcionario,201);
    }

    public function excluir($id) {
        $empresa = new EmpresaService();
        $empresa = $empresa->obterEmpresaUsuarioLogado();
        $funcionario = $this->getFuncionario($id);
        if($empresa->id != $funcionario->empresa_id){
            return response()->json(['mensagem' => 'Funcionario não vinculado a empresa CNPJ '.$empresa->cnpj],500);
        }        
        $funcionario->delete();
        return response()->json(['mensagem' => 'Excluído com sucesso'],200);
    }
    protected function getFuncionario($id)  {
        return $this->funcionario->find($id);
    }
}