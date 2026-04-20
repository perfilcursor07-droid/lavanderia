<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pesagem extends Model
{
    use HasFactory;

    protected $table = 'pesagens';

    // Status possíveis para pesagem
    const STATUS_RASCUNHO = 'rascunho';
    const STATUS_CONCLUIDA = 'concluida';

    protected $fillable = [
        'coleta_id',
        'usuario_id',
        'tipo_id',
        'peso',
        'quantidade',
        'peso_unitario',
        'data_pesagem',
        'observacoes',
        'local_pesagem',
        'status'
    ];

    protected $casts = [
        'peso' => 'decimal:2',
        'peso_unitario' => 'decimal:2',
        'quantidade' => 'integer',
        'data_pesagem' => 'datetime'
    ];

    /**
     * Relacionamento com coleta
     */
    public function coleta()
    {
        return $this->belongsTo(Coleta::class);
    }

    /**
     * Relacionamento com usuário responsável pela pesagem
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }



    /**
     * Relacionamento com tipo de peça
     */
    public function tipo()
    {
        return $this->belongsTo(Tipo::class);
    }

    /**
     * Boot method para calcular peso unitário automaticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($pesagem) {
            // Calcular peso unitário se quantidade > 0
            if ($pesagem->quantidade > 0) {
                $pesagem->peso_unitario = $pesagem->peso / $pesagem->quantidade;
            }

            // Definir data de pesagem se não informada
            if (!$pesagem->data_pesagem) {
                $pesagem->data_pesagem = now();
            }
        });

        static::saved(function ($pesagem) {
            // Recalcular totais da coleta após salvar pesagem
            if ($pesagem->coleta && $pesagem->coleta->exists) {
                $pesagem->coleta->calcularTotais();
            }
        });

        static::deleted(function ($pesagem) {
            // Recalcular totais da coleta após excluir pesagem
            if ($pesagem->coleta && $pesagem->coleta->exists) {
                $pesagem->coleta->calcularTotais();
            }
        });
    }

    /**
     * Scope para pesagens conferidas
     */
    public function scopeConferidas($query)
    {
        return $query->where('conferido', true);
    }

    /**
     * Scope para pesagens em rascunho
     */
    public function scopeRascunho($query)
    {
        return $query->where('status', self::STATUS_RASCUNHO);
    }

    /**
     * Scope para pesagens concluídas
     */
    public function scopeConcluidas($query)
    {
        return $query->where('status', self::STATUS_CONCLUIDA);
    }

    /**
     * Scope para pesagens não conferidas
     */
    public function scopeNaoConferidas($query)
    {
        return $query->where('conferido', false);
    }

    /**
     * Scope para pesagens por período
     */
    public function scopePorPeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_pesagem', [$dataInicio, $dataFim]);
    }

    /**
     * Scope para pesagens por coleta
     */
    public function scopePorColeta($query, $coletaId)
    {
        return $query->where('coleta_id', $coletaId);
    }

    /**
     * Scope para pesagens por tipo
     */
    public function scopePorTipo($query, $tipoId)
    {
        return $query->where('tipo_id', $tipoId);
    }

    /**
     * Scope para pesagens por usuário
     */
    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    /**
     * Accessor para peso formatado
     */
    public function getPesoFormatadoAttribute()
    {
        return number_format($this->peso, 2, ',', '.') . ' kg';
    }

    /**
     * Accessor para peso unitário formatado
     */
    public function getPesoUnitarioFormatadoAttribute()
    {
        return $this->peso_unitario ? number_format($this->peso_unitario, 2, ',', '.') . ' kg' : '-';
    }

    /**
     * Accessor para data de pesagem formatada
     */
    public function getDataPesagemFormatadaAttribute()
    {
        return $this->data_pesagem ? $this->data_pesagem->format('d/m/Y H:i') : '-';
    }





    /**
     * Verifica se a pesagem pode ser editada
     */
    public function podeSerEditada()
    {
        // Pesagem sempre pode ser editada, mesmo se concluída
        return true;
    }

    /**
     * Verifica se a pesagem está em rascunho
     */
    public function isRascunho()
    {
        return $this->status === self::STATUS_RASCUNHO;
    }

    /**
     * Verifica se a pesagem está concluída
     */
    public function isConcluida()
    {
        return $this->status === self::STATUS_CONCLUIDA;
    }

    /**
     * Conclui a pesagem
     */
    public function concluir()
    {
        $this->update(['status' => self::STATUS_CONCLUIDA]);
    }

    /**
     * Define a pesagem como rascunho
     */
    public function definirComoRascunho()
    {
        $this->update(['status' => self::STATUS_RASCUNHO]);
    }

    /**
     * Calcula a diferença percentual entre peso esperado e pesado
     */
    public function calcularDiferencaPercentual($pesoEsperado)
    {
        if ($pesoEsperado <= 0) {
            return null;
        }

        return (($this->peso - $pesoEsperado) / $pesoEsperado) * 100;
    }

    /**
     * Calcula o valor da pesagem baseado no preço do estabelecimento
     */
    public function getValorCalculadoAttribute()
    {
        if (!$this->coleta || !$this->coleta->estabelecimento) {
            return 0;
        }

        $estabelecimento = $this->coleta->estabelecimento;

        // Para pesagem, sempre usar preço por kg
        return $this->peso * $estabelecimento->preco_kg;
    }

    /**
     * Retorna o valor formatado
     */
    public function getValorFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor_calculado, 2, ',', '.');
    }
}
