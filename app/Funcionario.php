<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Empresa;
use App\Endereco;
use App\Profissao;

class Funcionario extends Model
{
    protected $fillable=[
        'id',        
        'usuario_id',
        'empresa_id',
        'endereco_id',
        'profissao_id',
        
    ];
    protected $table = 'funcionarios';

    public function usuario(){
        return $this->belongsTo(User::class);
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class);
    }
    public function endereco(){
        return $this->belongsTo(Endereco::class);
    }
    public function profissao(){
        return $this->belongsTo(profissao::class);
    }
}
