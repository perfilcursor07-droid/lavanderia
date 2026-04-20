<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\NivelAcesso;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Criar usuário administrador';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?: 'admin@lavanderia.com';
        $password = $this->argument('password') ?: 'admin123';

        // Verificar se usuário já existe
        if (Usuario::where('email', $email)->exists()) {
            $this->error("Usuário com email {$email} já existe!");
            return 1;
        }

        // Buscar ou criar nível de acesso Administrador
        $nivelAdmin = NivelAcesso::firstOrCreate(
            ['nome' => 'Administrador'],
            [
                'descricao' => 'Acesso completo ao sistema',
                'permissoes' => [
                    'usuarios.criar', 'usuarios.editar', 'usuarios.excluir', 'usuarios.visualizar',
                    'estabelecimentos.criar', 'estabelecimentos.editar', 'estabelecimentos.excluir', 'estabelecimentos.visualizar',
                    'coletas.criar', 'coletas.editar', 'coletas.cancelar', 'coletas.visualizar',
                    'pesagem.criar', 'pesagem.editar', 'pesagem.visualizar',
                    'empacotamento.criar', 'empacotamento.editar', 'empacotamento.visualizar',
                    'relatorios.visualizar', 'relatorios.exportar'
                ],
                'ativo' => true
            ]
        );

        // Criar usuário admin
        $admin = Usuario::create([
            'nome' => 'Administrador do Sistema',
            'email' => $email,
            'password' => Hash::make($password),
            'telefone' => '(11) 99999-9999',
            'cpf' => '000.000.000-00',
            'nivel_acesso_id' => $nivelAdmin->id,
            'ativo' => true,
            'email_verified_at' => now()
        ]);

        $this->info("✅ Usuário administrador criado com sucesso!");
        $this->info("📧 Email: {$email}");
        $this->info("🔑 Senha: {$password}");
        $this->info("🆔 ID: {$admin->id}");

        return 0;
    }
}
