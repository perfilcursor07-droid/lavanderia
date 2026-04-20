<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColetaPeca extends Model
{
    use HasFactory;

    protected $table = 'coleta_pecas';

    protected $fillable = [
        'coleta_id',
        'tipo_id',
        'quantidade',
        'peso',
        'observacoes',
        'relave',
        'desengoma'
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'peso' => 'decimal:2',
        'relave' => 'boolean',
        'desengoma' => 'boolean'
    ];

    /**
     * Relacionamento com coleta
     */
    public function coleta()
    {
        return $this->belongsTo(Coleta::class);
    }

    /**
     * Relacionamento com tipo
     */
    public function tipo()
    {
        return $this->belongsTo(Tipo::class);
    }

    /**
     * Boot method para eventos do modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($coletaPeca) {
            // Recalcular totais da coleta apenas se a coleta existir
            if ($coletaPeca->coleta && $coletaPeca->coleta->exists) {
                $coletaPeca->coleta->calcularTotais();
            }
        });

        static::deleted(function ($coletaPeca) {
            // Recalcular totais da coleta após exclusão
            if ($coletaPeca->coleta && $coletaPeca->coleta->exists) {
                $coletaPeca->coleta->calcularTotais();
            }
        });
    }



    /**
     * Accessor para subtotal formatado (agora representa quantidade de peças)
     */
    public function getSubtotalFormatadoAttribute()
    {
        return $this->quantidade . ($this->quantidade == 1 ? ' peça' : ' peças');
    }

    /**
     * Scope para peças relave
     */
    public function scopeRelave($query)
    {
        return $query->where('relave', true);
    }

    /**
     * Scope para peças desengoma
     */
    public function scopeDesengoma($query)
    {
        return $query->where('desengoma', true);
    }

    /**
     * Scope para peças normais (não relave nem desengoma)
     */
    public function scopeNormal($query)
    {
        return $query->where('relave', false)->where('desengoma', false);
    }

    /**
     * Verifica se a peça é cobrável (não relave)
     */
    public function isCobravel()
    {
        return !$this->relave;
    }

    /**
     * Gera descrição do tipo da peça com marcações especiais
     */
    public function getDescricaoTipoAttribute()
    {
        $descricao = $this->tipo->nome;
        
        if ($this->relave) {
            $descricao .= ' (RELAVE)';
        }
        
        if ($this->desengoma) {
            $descricao .= ' (DESENGOMA)';
        }
        
        return $descricao;
    }
}
