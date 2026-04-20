<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\NivelAcesso;
use Illuminate\Support\Facades\Hash;

class UsuarioAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nivelAdmin = NivelAcesso::where('nome', 'Administrador')->first();
        
        if (!$nivelAdmin) {
            $this->command->error('Nível de acesso Administrador não encontrado. Execute NiveisAcessoSeeder primeiro.');
            return;
        }
        
        Usuario::updateOrCreate(
            ['email' => 'admin@lavanderia.com'], // Busca por email
            [
                'nome' => 'Administrador do Sistema',
                'email' => 'admin@lavanderia.com',
                'password' => Hash::make('admin123'),
                'telefone' => '(11) 99999-9999',
                'cpf' => '000.000.000-00',
                'nivel_acesso_id' => $nivelAdmin->id,
                'ativo' => true,
                'email_verified_at' => now()
            ]
        );
    }
}
