<?php
namespace App\Http\Repository;
use App\User;

class UserRepository
{
    public $userService;
    public function __construct()  {    
    }
    public function obterPorId($id){
        $usuario = User::where('id',$id)->first();
        return $usuario;
    }
   
}
