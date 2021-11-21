<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profissao extends Model
{
    protected $fillable=[
        'id',
        'descricao',
        'ativo'          
    ];
    protected $table = 'profissoes';
}
