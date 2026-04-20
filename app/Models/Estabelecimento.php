<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estabelecimento extends Model
{
    use HasFactory;

    protected $table = 'estabelecimentos';

    protected $fillable = [
        'cnpj',
        'razao_social',
        'nome_fantasia',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'telefone',
        'emails',
        'contatos_responsaveis',
        'observacoes',
        'ativo',
        'tipo_precificacao',
        'preco_kg',
        'preco_peca',
        'observacoes_preco',
    ];

    protected $casts = [
        'emails' => 'array',
        'contatos_responsaveis' => 'array',
        'ativo' => 'boolean',
        'preco_kg' => 'decimal:2',
        'preco_peca' => 'decimal:2',
    ];

    /**
     * Relacionamento com coletas
     */
    public function coletas()
    {
        return $this->hasMany(Coleta::class);
    }

    /**
     * Scope para estabelecimentos ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Accessor para endereço completo
     */
    public function getEnderecoCompletoAttribute()
    {
        $endereco = $this->endereco . ', ' . $this->numero;
        
        if ($this->complemento) {
            $endereco .= ', ' . $this->complemento;
        }
        
        $endereco .= ' - ' . $this->bairro . ', ' . $this->cidade . '/' . $this->estado;
        $endereco .= ' - CEP: ' . $this->cep;
        
        return $endereco;
    }

    /**
     * Mutator para CNPJ (remove formatação)
     */
    public function setCnpjAttribute($value)
    {
        $this->attributes['cnpj'] = preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * Mutator para CEP (remove formatação)
     */
    public function setCepAttribute($value)
    {
        $this->attributes['cep'] = preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * Accessor para CEP formatado
     */
    public function getCepFormatadoAttribute()
    {
        $cep = $this->cep;
        return substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
    }

    /**
     * Accessor para CNPJ formatado
     */
    public function getCnpjFormatadoAttribute()
    {
        $cnpj = $this->cnpj;
        if (strlen($cnpj) !== 14) {
            return $cnpj;
        }
        return substr($cnpj, 0, 2) . '.' . 
               substr($cnpj, 2, 3) . '.' . 
               substr($cnpj, 5, 3) . '/' . 
               substr($cnpj, 8, 4) . '-' . 
               substr($cnpj, 12, 2);
    }
}
