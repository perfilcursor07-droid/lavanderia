<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'email',
        'password',
        'telefone',
        'cpf',
        'nivel_acesso_id',
        'ativo',
        'ultimo_login'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'ultimo_login' => 'datetime',
        'ativo' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Relacionamento com nível de acesso
     */
    public function nivelAcesso()
    {
        return $this->belongsTo(NivelAcesso::class);
    }

    /**
     * Relacionamento com coletas criadas
     */
    public function coletas()
    {
        return $this->hasMany(Coleta::class);
    }

    /**
     * Relacionamento com empacotamentos feitos
     */
    public function empacotamentos()
    {
        return $this->hasMany(Empacotamento::class, 'usuario_empacotamento_id');
    }

    /**
     * Relacionamento com entregas como motorista
     */
    public function entregas()
    {
        return $this->hasMany(Empacotamento::class, 'motorista_id');
    }

    /**
     * Verifica se o usuário tem uma permissão específica
     */
    public function temPermissao($permissao)
    {
        return $this->nivelAcesso && $this->nivelAcesso->temPermissao($permissao);
    }

    /**
     * Scope para usuários ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para motoristas
     */
    public function scopeMotoristas($query)
    {
        return $query->whereHas('nivelAcesso', function($q) {
            $q->where('nome', 'Motorista');
        });
    }


    /**
     * Retorna CPF sem formatação
     */
    public function getCpfLimpoAttribute()
    {
        return preg_replace('/[^0-9]/', '', $this->attributes['cpf'] ?? '');
    }

    /**
     * Retorna CPF formatado
     */
    public function getCpfFormatadoAttribute()
    {
        $cpf = $this->getCpfLimpoAttribute();
        if (strlen($cpf) !== 11) return $this->attributes['cpf'];
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }
}
