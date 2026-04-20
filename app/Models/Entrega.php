<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    use HasFactory;

    protected $fillable = [
        'empacotamento_id',
        'motorista_saida_id',
        'motorista_entrega_id',
        'status_id',
        'data_saida',
        'data_entrega',
        'data_confirmacao_recebimento',
        'nome_recebedor',
        'assinatura_recebedor',
        'assinatura_cliente',
        'observacoes_entrega'
    ];

    protected $casts = [
        'data_saida' => 'datetime',
        'data_entrega' => 'datetime',
        'data_confirmacao_recebimento' => 'datetime'
    ];

    /**
     * Relacionamento com empacotamento
     */
    public function empacotamento()
    {
        return $this->belongsTo(Empacotamento::class);
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
     * Verifica se a entrega foi confirmada pelo cliente
     */
    public function foiConfirmadaPeloCliente()
    {
        return $this->status->nome === 'Confirmado pelo Cliente';
    }

    /**
     * Verifica se a entrega foi realizada
     */
    public function foiEntregue()
    {
        return in_array($this->status->nome, ['Entregue', 'Confirmado pelo Cliente']);
    }

    /**
     * Verifica se está em trânsito
     */
    public function estaEmTransito()
    {
        return $this->status->nome === 'Em trânsito';
    }

    /**
     * Scope para entregas por período
     */
    public function scopePorPeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_entrega', [$dataInicio, $dataFim]);
    }

    /**
     * Scope para entregas por motorista
     */
    public function scopePorMotorista($query, $motoristaId)
    {
        return $query->where('motorista_entrega_id', $motoristaId);
    }

    /**
     * Scope para entregas por status
     */
    public function scopePorStatus($query, $statusId)
    {
        return $query->where('status_id', $statusId);
    }
}
