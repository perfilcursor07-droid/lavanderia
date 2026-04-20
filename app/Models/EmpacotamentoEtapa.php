<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpacotamentoEtapa extends Model
{
    use HasFactory;

    protected $table = 'empacotamento_etapas';

    protected $fillable = [
        'empacotamento_id',
        'tipo_id',
        'usuario_responsavel_id',
        'status',
        'data_inicio',
        'data_finalizacao',
        'observacoes'
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_finalizacao' => 'datetime',
    ];

    /**
     * Relacionamento com empacotamento
     */
    public function empacotamento()
    {
        return $this->belongsTo(Empacotamento::class);
    }

    /**
     * Relacionamento com tipo de peça
     */
    public function tipo()
    {
        return $this->belongsTo(Tipo::class);
    }

    /**
     * Relacionamento com usuário responsável
     */
    public function usuarioResponsavel()
    {
        return $this->belongsTo(Usuario::class, 'usuario_responsavel_id');
    }

    /**
     * Scope para etapas em andamento
     */
    public function scopeEmAndamento($query)
    {
        return $query->where('status', 'em_andamento');
    }

    /**
     * Scope para etapas finalizadas
     */
    public function scopeFinalizado($query)
    {
        return $query->where('status', 'finalizado');
    }

    /**
     * Scope por usuário responsável
     */
    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_responsavel_id', $usuarioId);
    }

    /**
     * Scope por empacotamento
     */
    public function scopePorEmpacotamento($query, $empacotamentoId)
    {
        return $query->where('empacotamento_id', $empacotamentoId);
    }

    /**
     * Finaliza a etapa
     */
    public function finalizar($observacoes = null)
    {
        $this->update([
            'status' => 'finalizado',
            'data_finalizacao' => now(),
            'observacoes' => $observacoes ?? $this->observacoes
        ]);

        // Marcar o tipo como finalizado no empacotamento
        $this->empacotamento->finalizarTipo($this->tipo_id);
    }

    /**
     * Reabrir a etapa
     */
    public function reabrir()
    {
        $this->update([
            'status' => 'em_andamento',
            'data_finalizacao' => null
        ]);

        // Reabrir o tipo no empacotamento
        $this->empacotamento->reabrirTipo($this->tipo_id);
    }

    /**
     * Calcula duração da etapa (em minutos)
     */
    public function getDuracaoMinutosAttribute()
    {
        if (!$this->data_inicio) {
            return null;
        }

        $dataFim = $this->data_finalizacao ?? now();
        return $this->data_inicio->diffInMinutes($dataFim);
    }

    /**
     * Calcula duração formatada da etapa
     */
    public function getDuracaoFormatadaAttribute()
    {
        $minutos = $this->duracao_minutos;
        
        if ($minutos === null) {
            return null;
        }

        $horas = intval($minutos / 60);
        $minutosRestantes = $minutos % 60;

        if ($horas > 0) {
            return sprintf('%dh %02dm', $horas, $minutosRestantes);
        }

        return sprintf('%dm', $minutosRestantes);
    }

    /**
     * Verifica se a etapa está finalizada
     */
    public function isFinalizada()
    {
        return $this->status === 'finalizado';
    }

    /**
     * Verifica se a etapa está em andamento
     */
    public function isEmAndamento()
    {
        return $this->status === 'em_andamento';
    }
}
