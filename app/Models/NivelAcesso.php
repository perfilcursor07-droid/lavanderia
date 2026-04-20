<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelAcesso extends Model
{
    use HasFactory;

    protected $table = 'niveis_acesso';

    protected $fillable = [
        'nome',
        'descricao',
        'permissoes',
        'ativo'
    ];

    protected $casts = [
        'permissoes' => 'array',
        'ativo' => 'boolean'
    ];

    /**
     * Relacionamento com usuários
     */
    public function usuarios()
    {
        return $this->hasMany(Usuario::class);
    }

    /**
     * Verifica se o nível tem uma permissão específica
     */
    public function temPermissao($permissao)
    {
        return in_array($permissao, $this->permissoes ?? []);
    }

    /**
     * Scope para níveis ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }
}
