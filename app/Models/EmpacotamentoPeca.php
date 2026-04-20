<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmpacotamentoPeca extends Model
{
    use HasFactory;

    protected $table = 'empacotamento_pecas';

    protected $fillable = [
        'empacotamento_id',
        'tipo_id',
        'codigo_qr',
        'quantidade',
        'peso',
        'observacoes',
        'status_saida',
        'data_saida',
        'motorista_saida_id',
        'data_entrega',
        'motorista_entrega_id',
        'nome_recebedor',
        'assinatura_recebedor',
        'relave',
        'inutilizada',
        'impresso',
        'data_impressao',
        'responsavel_empacotamento_id'
    ];

    protected $casts = [
        'data_saida' => 'datetime',
        'data_entrega' => 'datetime',
        'data_impressao' => 'datetime',
        'relave' => 'boolean',
        'inutilizada' => 'boolean',
        'impresso' => 'boolean',
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
     * Relacionamento com responsável pelo empacotamento
     */
    public function responsavelEmpacotamento()
    {
        return $this->belongsTo(Usuario::class, 'responsavel_empacotamento_id');
    }

    /**
     * Relacionamento com motorista que fez a saída
     */
    public function motoristaSaida()
    {
        return $this->belongsTo(Usuario::class, 'motorista_saida_id');
    }

    /**
     * Relacionamento com motorista que fez a entrega
     */
    public function motoristaEntrega()
    {
        return $this->belongsTo(Usuario::class, 'motorista_entrega_id');
    }

    /**
     * Boot method para gerar código QR automaticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($empacotamentoPeca) {
            if (!$empacotamentoPeca->codigo_qr) {
                do {
                    $codigo = 'PC' . strtoupper(Str::random(8));
                } while (static::where('codigo_qr', $codigo)->exists());
                
                $empacotamentoPeca->codigo_qr = $codigo;
            }
        });
    }

    /**
     * Gera URL do QR Code
     */
    public function getUrlQrCodeAttribute()
    {
        return route('qrcodes.rastrear-peca', $this->codigo_qr);
    }

    /**
     * Scope para peças por empacotamento
     */
    public function scopePorEmpacotamento($query, $empacotamentoId)
    {
        return $query->where('empacotamento_id', $empacotamentoId);
    }

    /**
     * Scope para peças por tipo
     */
    public function scopePorTipo($query, $tipoId)
    {
        return $query->where('tipo_id', $tipoId);
    }

    /**
     * Scope para peças relave
     */
    public function scopeRelave($query)
    {
        return $query->where('relave', true);
    }

    /**
     * Scope para peças inutilizadas
     */
    public function scopeInutilizada($query)
    {
        return $query->where('inutilizada', true);
    }

    /**
     * Scope para peças impressas
     */
    public function scopeImpresso($query)
    {
        return $query->where('impresso', true);
    }

    /**
     * Scope para peças não impressas
     */
    public function scopeNaoImpresso($query)
    {
        return $query->where('impresso', false);
    }

    /**
     * Marca a peça como impressa
     */
    public function marcarComoImpresso()
    {
        $this->update([
            'impresso' => true,
            'data_impressao' => now()
        ]);
    }

    /**
     * Gera descrição completa da peça para etiqueta
     */
    public function getDescricaoEtiquetaAttribute()
    {
        $descricao = "{$this->empacotamento->coleta->estabelecimento->nome_fantasia}\n";
        $descricao .= "{$this->tipo->nome}\n";
        $descricao .= "Qtd: {$this->quantidade}\n";
        $descricao .= "Data: " . $this->empacotamento->data_empacotamento->format('d/m/Y') . "\n";
        
        if ($this->responsavelEmpacotamento) {
            $descricao .= "Resp: {$this->responsavelEmpacotamento->nome}\n";
        }
        
        if ($this->relave) {
            $descricao .= "RELAVE\n";
        }
        
        if ($this->inutilizada) {
            $descricao .= "INUTILIZADA\n";
        }
        
        return $descricao;
    }

    /**
     * Verifica se pode ser reimpresso
     */
    public function podeSerReimpresso()
    {
        return $this->impresso;
    }
}
