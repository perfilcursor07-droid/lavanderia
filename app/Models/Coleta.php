<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coleta extends Model
{
    use HasFactory;

    protected $table = 'coletas';

    protected $fillable = [
        'estabelecimento_id',
        'usuario_id',
        'status_id',
        'data_agendamento',
        'data_coleta',
        'data_conclusao',
        'observacoes',
        'acompanhante',
        'motivo_cancelamento',
        'peso_total',
        'valor_total',
        'numero_coleta',
        'tipo_coleta',
        'data_prazo_entrega'
    ];

    protected $casts = [
        'data_agendamento' => 'datetime',
        'data_coleta' => 'datetime',
        'data_conclusao' => 'datetime',
        'peso_total' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'data_prazo_entrega' => 'date'
    ];

    /**
     * Relacionamento com estabelecimento
     */
    public function estabelecimento()
    {
        return $this->belongsTo(Estabelecimento::class);
    }

    /**
     * Relacionamento com usuário
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Relacionamento com status
     */
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Relacionamento com peças da coleta
     */
    public function pecas()
    {
        return $this->hasMany(ColetaPeca::class);
    }

    /**
     * Relacionamento com empacotamentos
     */
    public function empacotamentos()
    {
        return $this->hasMany(Empacotamento::class);
    }

    /**
     * Relacionamento com empacotamento (singular para compatibilidade)
     */
    public function empacotamento()
    {
        return $this->hasOne(Empacotamento::class);
    }

    /**
     * Relacionamento com pesagens
     */
    public function pesagens()
    {
        return $this->hasMany(Pesagem::class);
    }

    /**
     * Boot method para gerar número da coleta automaticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($coleta) {
            if (!$coleta->numero_coleta) {
                $ultimaColeta = static::orderBy('id', 'desc')->first();
                $proximoNumero = $ultimaColeta ? (intval(substr($ultimaColeta->numero_coleta, 3)) + 1) : 1;
                $coleta->numero_coleta = 'COL' . str_pad($proximoNumero, 6, '0', STR_PAD_LEFT);
            }
        });

        // Remover o evento saved para evitar loop infinito
    }

    /**
     * Verifica se a coleta pode ser cancelada
     */
    public function podeSerCancelada()
    {
        return in_array($this->status->nome, ['Agendada', 'Em andamento']);
    }

    /**
     * Verifica se a coleta pode ser editada
     */
    public function podeSerEditada()
    {
        // Coleta pode ser editada se não estiver concluída, cancelada ou entregue
        return !in_array($this->status->nome, ['Concluída', 'Cancelada', 'Entregue']);
    }

    /**
     * Calcula os totais da coleta baseado nas peças e pesagens
     */
    public function calcularTotais()
    {
        // Carregar relacionamentos necessários
        $this->load(['estabelecimento', 'pesagens']);
        
        // Calcular peso total (peças + pesagens)
        $pesoTotalPecas = $this->pecas->sum('peso');
        $pesoTotalPesagens = $this->pesagens->sum('peso');
        $pesoTotal = $pesoTotalPecas + $pesoTotalPesagens;
        
        $valorTotal = 0;

        // Calcular valor total baseado no tipo de precificação do estabelecimento
        if ($this->estabelecimento) {
            // Valor das peças
            if ($this->estabelecimento->tipo_precificacao === 'peso') {
                $valorTotal += $pesoTotalPecas * $this->estabelecimento->preco_kg;
            } elseif ($this->estabelecimento->tipo_precificacao === 'peca') {
                $quantidadeTotal = $this->pecas->sum('quantidade');
                $valorTotal += $quantidadeTotal * $this->estabelecimento->preco_peca;
            }
            
            // Valor das pesagens (sempre por peso)
            $valorTotal += $pesoTotalPesagens * $this->estabelecimento->preco_kg;
        }

        // Usar updateQuietly para evitar disparar eventos e loop infinito
        $this->updateQuietly([
            'peso_total' => $pesoTotal,
            'valor_total' => $valorTotal,
        ]);
    }

    /**
     * Scope para coletas ativas (não canceladas)
     */
    public function scopeAtivas($query)
    {
        return $query->whereHas('status', function($q) {
            $q->where('nome', '!=', 'Cancelada');
        });
    }

    /**
     * Scope para coletas por período
     */
    public function scopePorPeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_agendamento', [$dataInicio, $dataFim]);
    }

    /**
     * Scope para coletas por estabelecimento
     */
    public function scopePorEstabelecimento($query, $estabelecimentoId)
    {
        return $query->where('estabelecimento_id', $estabelecimentoId);
    }

    /**
     * Scope para coletas por status
     */
    public function scopePorStatus($query, $statusId)
    {
        return $query->where('status_id', $statusId);
    }

    /**
     * Verifica se a coleta pode ser concluída
     */
    public function podeSerConcluida()
    {
        return $this->status->nome === 'Em andamento';
    }

    /**
     * Verifica se a coleta pode ter pesagens registradas
     */
    public function podeReceberPesagens()
    {
        return in_array($this->status->nome, ['Concluída', 'Em andamento']);
    }

    /**
     * Calcula o peso total das pesagens
     */
    public function pesoTotalPesagens()
    {
        return $this->pesagens->sum('peso');
    }

    /**
     * Verifica se a coleta já possui pesagem cadastrada
     */
    public function possuiPesagem()
    {
        return $this->pesagens()->exists();
    }

    /**
     * Scope para coletas sem pesagem
     */
    public function scopeSemPesagem($query)
    {
        return $query->whereDoesntHave('pesagens');
    }

    /**
     * Verifica se há diferença entre peso das peças e pesagens
     */
    public function temDiferencaPeso()
    {
        $pesoPecas = $this->peso_total;
        $pesoPesagens = $this->pesoTotalPesagens();

        return abs($pesoPecas - $pesoPesagens) > 0.01; // Tolerância de 10g
    }

    /**
     * Calcula a diferença percentual entre peso das peças e pesagens
     */
    public function diferencaPercentualPeso()
    {
        $pesoPecas = $this->peso_total;
        $pesoPesagens = $this->pesoTotalPesagens();

        if ($pesoPecas <= 0) {
            return null;
        }

        return (($pesoPesagens - $pesoPecas) / $pesoPecas) * 100;
    }

    /**
     * Scope para coletas por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_coleta', $tipo);
    }

    /**
     * Scope para coletas normais
     */
    public function scopeNormal($query)
    {
        return $query->where('tipo_coleta', 'normal');
    }

    /**
     * Scope para coletas de desengoma
     */
    public function scopeDesengoma($query)
    {
        return $query->where('tipo_coleta', 'desengoma');
    }

    /**
     * Scope para coletas de relave
     */
    public function scopeRelave($query)
    {
        return $query->where('tipo_coleta', 'relave');
    }

    /**
     * Verifica se é coleta de desengoma
     */
    public function isDesengoma()
    {
        return $this->tipo_coleta === 'desengoma';
    }

    /**
     * Verifica se é coleta de relave
     */
    public function isRelave()
    {
        return $this->tipo_coleta === 'relave';
    }

    /**
     * Verifica se é coleta normal
     */
    public function isNormal()
    {
        return $this->tipo_coleta === 'normal';
    }

    /**
     * Calcula quantas peças relave foram coletadas
     */
    public function totalPecasRelave()
    {
        return $this->pecas()->relave()->sum('quantidade');
    }

    /**
     * Calcula quantas peças desengoma foram coletadas
     */
    public function totalPecasDesengoma()
    {
        return $this->pecas()->desengoma()->sum('quantidade');
    }

    /**
     * Calcula quantas peças normais (cobráveis) foram coletadas
     */
    public function totalPecasCobráveis()
    {
        return $this->pecas()->normal()->sum('quantidade');
    }

    /**
     * Gera descrição do tipo de coleta
     */
    public function getDescricaoTipoColetaAttribute()
    {
        switch ($this->tipo_coleta) {
            case 'desengoma':
                return 'Desengoma';
            case 'relave':
                return 'Relave';
            default:
                return 'Normal';
        }
    }

    /**
     * Verifica se a coleta tem prazo especial
     */
    public function temPrazoEspecial()
    {
        return $this->data_prazo_entrega !== null;
    }

    /**
     * Calcula o valor total da coleta baseado no tipo de precificação
     * Agora usa o valor salvo no banco, calculado pelo método calcularTotais()
     */
    public function getValorTotalAttribute($value)
    {
        // Se há um valor salvo no banco, usar ele
        if (isset($this->attributes['valor_total']) && $this->attributes['valor_total'] > 0) {
            return $this->attributes['valor_total'];
        }

        // Caso contrário, calcular dinamicamente (fallback)
        if (!$this->estabelecimento) {
            return 0;
        }

        $estabelecimento = $this->estabelecimento;
        $valorTotal = 0;

        // Valor das peças
        if ($estabelecimento->tipo_precificacao === 'peso') {
            $pesoTotalPecas = $this->pecas()->sum('peso');
            $valorTotal += $pesoTotalPecas * $estabelecimento->preco_kg;
        } elseif ($estabelecimento->tipo_precificacao === 'peca') {
            $quantidadeTotalPecas = $this->pecas()->sum('quantidade');
            $valorTotal += $quantidadeTotalPecas * $estabelecimento->preco_peca;
        }

        // Valor das pesagens (sempre por peso)
        $pesoTotalPesagens = $this->pesagens()->sum('peso');
        $valorTotal += $pesoTotalPesagens * $estabelecimento->preco_kg;

        return $valorTotal;
    }

    /**
     * Retorna o valor total formatado
     */
    public function getValorTotalFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor_total, 2, ',', '.');
    }
}
