<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Anotacao extends Model
{
    use HasFactory;

    protected $table = 'anotacoes';

    protected $fillable = [
        'usuario_id',
        'modulo',
        'pagina',
        'pagina_nome',
        'categoria',
        'texto',
        'resolvida',
        'data_resolucao',
        'observacao_resolucao',
    ];

    protected $casts = [
        'resolvida' => 'boolean',
        'data_resolucao' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento com usuário
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Scope para anotações por módulo
     */
    public function scopePorModulo($query, $modulo)
    {
        return $query->where('modulo', $modulo);
    }

    /**
     * Scope para anotações por categoria
     */
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Scope para anotações não resolvidas
     */
    public function scopeNaoResolvidas($query)
    {
        return $query->where('resolvida', false);
    }

    /**
     * Scope para anotações resolvidas
     */
    public function scopeResolvidas($query)
    {
        return $query->where('resolvida', true);
    }

    /**
     * Scope para anotações do usuário atual
     */
    public function scopeDoUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    /**
     * Marcar como resolvida
     */
    public function marcarComoResolvida($observacao = null)
    {
        $this->update([
            'resolvida' => true,
            'data_resolucao' => now(),
            'observacao_resolucao' => $observacao,
        ]);
    }

    /**
     * Marcar como não resolvida
     */
    public function marcarComoNaoResolvida()
    {
        $this->update([
            'resolvida' => false,
            'data_resolucao' => null,
            'observacao_resolucao' => null,
        ]);
    }

    /**
     * Accessor para nome do módulo formatado
     */
    public function getModuloFormatadoAttribute()
    {
        $modulos = [
            'estabelecimentos' => 'Estabelecimentos',
            'coletas' => 'Coletas',
            'empacotamento' => 'Empacotamento',
            'painel' => 'Painel Dashboard',
            'usuarios' => 'Usuários',
            'relatorios' => 'Relatórios',
            'configuracoes' => 'Configurações',
            'geral' => 'Geral',
        ];

        return $modulos[$this->modulo] ?? ucfirst($this->modulo);
    }

    /**
     * Accessor para categoria formatada
     */
    public function getCategoriaFormatadaAttribute()
    {
        $categorias = [
            'melhorias' => 'Melhorias',
            'alteracoes' => 'Alterações',
            'exclusoes' => 'Exclusões',
        ];

        return $categorias[$this->categoria] ?? ucfirst($this->categoria);
    }

    /**
     * Accessor para ícone da categoria
     */
    public function getCategoriaIconeAttribute()
    {
        $icones = [
            'melhorias' => '✨',
            'alteracoes' => '🔧',
            'exclusoes' => '🗑️',
        ];

        return $icones[$this->categoria] ?? '📝';
    }

    /**
     * Accessor para cor da categoria
     */
    public function getCategoriaCorAttribute()
    {
        $cores = [
            'melhorias' => 'green',
            'alteracoes' => 'yellow',
            'exclusoes' => 'red',
        ];

        return $cores[$this->categoria] ?? 'gray';
    }

    /**
     * Accessor para data formatada
     */
    public function getDataFormatadaAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    /**
     * Accessor para tempo relativo
     */
    public function getTempoRelativoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
