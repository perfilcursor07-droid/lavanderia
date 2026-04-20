<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status';

    protected $fillable = [
        'nome',
        'descricao',
        'tipo',
        'cor',
        'ordem',
        'ativo'
    ];

    protected $casts = [
        'ordem' => 'integer',
        'ativo' => 'boolean'
    ];

    /**
     * Relacionamento com coletas
     */
    public function coletas()
    {
        return $this->hasMany(Coleta::class);
    }

    /**
     * Relacionamento com empacotamentos
     */
    public function empacotamentos()
    {
        return $this->hasMany(Empacotamento::class);
    }

    /**
     * Scope para status ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope ordenado
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('ordem');
    }
}
