<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Empacotamento extends Model
{
    use HasFactory;

    protected $table = 'empacotamento';

    protected $fillable = [
        'coleta_id',
        'usuario_empacotamento_id',
        'motorista_id',
        'motorista_saida_id',
        'motorista_entrega_id',
        'status_id',
        'codigo_qr',
        'data_empacotamento',
        'data_saida',
        'data_entrega',
        'data_confirmacao_recebimento',
        'assinatura_recebimento',
        'assinatura_recebedor',
        'nome_recebedor',
        'observacoes_empacotamento',
        'observacoes_entrega',
        'tipos_finalizados',
        'progresso_percentual'
    ];

    protected $casts = [
        'data_empacotamento' => 'datetime',
        'data_saida' => 'datetime',
        'data_entrega' => 'datetime',
        'data_confirmacao_recebimento' => 'datetime',
        'tipos_finalizados' => 'array',
        'progresso_percentual' => 'decimal:2'
    ];

    /**
     * Relacionamento com coleta
     */
    public function coleta()
    {
        return $this->belongsTo(Coleta::class);
    }

    /**
     * Relacionamento com usuário que fez o empacotamento
     */
    public function usuarioEmpacotamento()
    {
        return $this->belongsTo(Usuario::class, 'usuario_empacotamento_id');
    }

    /**
     * Relacionamento com motorista
     */
    public function motorista()
    {
        return $this->belongsTo(Usuario::class, 'motorista_id');
    }

    /**
     * Relacionamento com motorista que confirmou saída
     */
    public function motoristaSaida()
    {
        return $this->belongsTo(Usuario::class, 'motorista_saida_id');
    }

    /**
     * Relacionamento com motorista que confirmou entrega
     */
    public function motoristaEntrega()
    {
        return $this->belongsTo(Usuario::class, 'motorista_entrega_id');
    }

    /**
     * Relacionamento com status
     */
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Relacionamento com entrega
     */
    public function entrega()
    {
        return $this->hasOne(Entrega::class);
    }

    /**
     * Relacionamento com peças individuais do empacotamento
     */
    public function pecasIndividuais()
    {
        return $this->hasMany(EmpacotamentoPeca::class);
    }

    /**
     * Relacionamento com etapas do empacotamento
     */
    public function etapas()
    {
        return $this->hasMany(EmpacotamentoEtapa::class);
    }

    /**
     * Boot method para gerar código QR automaticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($empacotamento) {
            if (!$empacotamento->codigo_qr) {
                do {
                    $codigo = 'EMP' . strtoupper(Str::random(8));
                } while (static::where('codigo_qr', $codigo)->exists());
                
                $empacotamento->codigo_qr = $codigo;
            }
        });
    }

    /**
     * Scope para empacotamentos por período
     */
    public function scopePorPeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_empacotamento', [$dataInicio, $dataFim]);
    }

    /**
     * Scope para empacotamentos por motorista
     */
    public function scopePorMotorista($query, $motoristaId)
    {
        return $query->where('motorista_id', $motoristaId);
    }

    /**
     * Scope para empacotamentos por status
     */
    public function scopePorStatus($query, $statusId)
    {
        return $query->where('status_id', $statusId);
    }

    /**
     * Verifica se o empacotamento pode ser entregue
     */
    public function podeSerEntregue()
    {
        return $this->status->nome === 'Pronto para entrega' || $this->status->nome === 'Em trânsito';
    }

    /**
     * Verifica se o empacotamento foi entregue
     */
    public function foiEntregue()
    {
        return $this->status->nome === 'Entregue';
    }

    /**
     * Gera URL do QR Code
     */
    public function getUrlQrCodeAttribute()
    {
        return route('qrcodes.rastrear', $this->codigo_qr);
    }

    /**
     * Formata a data de empacotamento
     */
    public function getDataEmpacotamentoFormatadaAttribute()
    {
        return $this->data_empacotamento ? $this->data_empacotamento->format('d/m/Y H:i') : null;
    }

    /**
     * Formata a data de entrega
     */
    public function getDataEntregaFormatadaAttribute()
    {
        return $this->data_entrega ? $this->data_entrega->format('d/m/Y H:i') : null;
    }

    /**
     * Verifica se pode ser editado
     */
    public function podeSerEditado()
    {
        return !in_array($this->status->nome, ['Entregue', 'Cancelado']);
    }

    /**
     * Finaliza um tipo de peça no empacotamento
     */
    public function finalizarTipo($tipoId)
    {
        $tiposFinalizados = $this->tipos_finalizados ?? [];
        
        if (!in_array($tipoId, $tiposFinalizados)) {
            $tiposFinalizados[] = $tipoId;
            $this->tipos_finalizados = $tiposFinalizados;
            $this->calcularProgresso();
            $this->save();
        }
    }

    /**
     * Reabrir um tipo de peça no empacotamento
     */
    public function reabrirTipo($tipoId)
    {
        $tiposFinalizados = $this->tipos_finalizados ?? [];
        
        if (($key = array_search($tipoId, $tiposFinalizados)) !== false) {
            unset($tiposFinalizados[$key]);
            $this->tipos_finalizados = array_values($tiposFinalizados);
            $this->calcularProgresso();
            $this->save();
        }
    }

    /**
     * Verifica se um tipo foi finalizado
     */
    public function tipoFinalizado($tipoId)
    {
        return in_array($tipoId, $this->tipos_finalizados ?? []);
    }

    /**
     * Calcula o progresso do empacotamento baseado nos tipos finalizados
     */
    public function calcularProgresso()
    {
        $tiposDaColeta = $this->coleta->pecas->pluck('tipo_id')->unique();
        $totalTipos = $tiposDaColeta->count();
        
        if ($totalTipos === 0) {
            $this->progresso_percentual = 0;
            return;
        }
        
        $tiposFinalizados = count($this->tipos_finalizados ?? []);
        $progresso = ($tiposFinalizados / $totalTipos) * 100;
        
        $this->progresso_percentual = min(100, max(0, $progresso));
    }

    /**
     * Obter tipos da coleta que ainda não foram finalizados
     */
    public function getTiposNaoFinalizados()
    {
        $tiposDaColeta = $this->coleta->pecas->pluck('tipo_id')->unique();
        $tiposFinalizados = $this->tipos_finalizados ?? [];
        
        return $tiposDaColeta->diff($tiposFinalizados);
    }

    /**
     * Verifica se todos os tipos foram finalizados
     */
    public function todosTiposFinalizados()
    {
        return $this->getTiposNaoFinalizados()->isEmpty();
    }

    /**
     * Conta total de peças relave no empacotamento
     */
    public function totalPecasRelave()
    {
        return $this->pecasIndividuais()->relave()->sum('quantidade');
    }

    /**
     * Conta total de peças inutilizadas no empacotamento
     */
    public function totalPecasInutilizadas()
    {
        return $this->pecasIndividuais()->inutilizada()->sum('quantidade');
    }

    /**
     * Conta total de etiquetas impressas
     */
    public function totalEtiquetasImpressas()
    {
        return $this->pecasIndividuais()->impresso()->count();
    }

    /**
     * Conta total de etiquetas não impressas
     */
    public function totalEtiquetasNaoImpressas()
    {
        return $this->pecasIndividuais()->naoImpresso()->count();
    }

    /**
     * Obter funcionários que trabalharam no empacotamento
     */
    public function getFuncionariosEmpacotamento()
    {
        return $this->pecasIndividuais()
            ->whereNotNull('responsavel_empacotamento_id')
            ->with('responsavelEmpacotamento')
            ->get()
            ->pluck('responsavelEmpacotamento')
            ->unique('id');
    }

    /**
     * Verifica se a quantidade empacotada está completa
     * Retorna true se todas as peças coletadas foram completamente empacotadas
     */
    public function quantidadeEmpacotadaCompleta()
    {
        $pecasColeta = $this->coleta->pecas;
        $pecasEmpacotadas = $this->pecasIndividuais;
        
        foreach ($pecasColeta as $pecaColeta) {
            $quantidadeEmpacotadaTipo = $pecasEmpacotadas
                ->where('tipo_id', $pecaColeta->tipo_id)
                ->sum('quantidade');
            
            if ($quantidadeEmpacotadaTipo < $pecaColeta->quantidade) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Calcula a diferença entre quantidade coletada e empacotada por tipo
     * Retorna array com tipos que ainda faltam empacotar
     */
    public function getPecasFaltandoEmpacotar()
    {
        $pecasColeta = $this->coleta->pecas;
        $pecasEmpacotadas = $this->pecasIndividuais;
        $pecasFaltando = [];
        
        foreach ($pecasColeta as $pecaColeta) {
            $quantidadeEmpacotadaTipo = $pecasEmpacotadas
                ->where('tipo_id', $pecaColeta->tipo_id)
                ->sum('quantidade');
            
            $diferenca = $pecaColeta->quantidade - $quantidadeEmpacotadaTipo;
            
            if ($diferenca > 0) {
                $pecasFaltando[] = [
                    'tipo' => $pecaColeta->tipo,
                    'quantidade_coletada' => $pecaColeta->quantidade,
                    'quantidade_empacotada' => $quantidadeEmpacotadaTipo,
                    'quantidade_faltando' => $diferenca
                ];
            }
        }
        
        return $pecasFaltando;
    }

    /**
     * Verifica se o empacotamento está em aberto (com peças pendentes)
     */
    public function estaEmAberto()
    {
        return !$this->quantidadeEmpacotadaCompleta();
    }

    /**
     * Calcula o valor total do empacotamento quando for por peça
     */
    public function getValorCalculadoAttribute()
    {
        if (!$this->coleta || !$this->coleta->estabelecimento) {
            return 0;
        }

        $estabelecimento = $this->coleta->estabelecimento;

        // Só calcula se for por peça (no empacotamento)
        if ($estabelecimento->tipo_precificacao === 'peca') {
            $totalPecas = $this->pecasIndividuais()->sum('quantidade');
            return $totalPecas * $estabelecimento->preco_peca;
        }

        return 0;
    }

    /**
     * Retorna o valor formatado
     */
    public function getValorFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor_calculado, 2, ',', '.');
    }
}
