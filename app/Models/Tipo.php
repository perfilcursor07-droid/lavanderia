<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipo extends Model
{
    use HasFactory;

    protected $table = 'tipos';

    protected $fillable = [
        'nome',
        'descricao',
        'categoria',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean'
    ];

    /**
     * Relacionamento com peças de coleta
     */
    public function coletaPecas()
    {
        return $this->hasMany(ColetaPeca::class);
    }

    /**
     * Scope para tipos ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope por categoria
     */
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Retorna o tipo especial "Peso" para coletas por peso
     */
    public static function getTipoPeso()
    {
        return static::where('nome', 'Peso')->where('categoria', 'peso')->first();
    }

}
