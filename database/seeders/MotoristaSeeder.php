<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\NivelAcesso;
use Illuminate\Support\Facades\Hash;

class MotoristaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar o nível de acesso "Motorista"
        $nivelMotorista = NivelAcesso::where('nome', 'Motorista')->first();
        
        if (!$nivelMotorista) {
            $this->command->error('Nível de acesso "Motorista" não encontrado!');
            return;
        }

        // Criar usuários motoristas de exemplo
        $motoristas = [
            [
                'nome' => 'João Silva',
                'email' => 'joao.motorista@lavanderia.com',
                'password' => Hash::make('123456'),
                'telefone' => '(11) 99999-1111',
                'cpf' => '12345678901',
                'nivel_acesso_id' => $nivelMotorista->id,
                'ativo' => true,
            ],
            [
                'nome' => 'Maria Santos',
                'email' => 'maria.motorista@lavanderia.com',
                'password' => Hash::make('123456'),
                'telefone' => '(11) 99999-2222',
                'cpf' => '12345678902',
                'nivel_acesso_id' => $nivelMotorista->id,
                'ativo' => true,
            ],
            [
                'nome' => 'Carlos Oliveira',
                'email' => 'carlos.motorista@lavanderia.com',
                'password' => Hash::make('123456'),
                'telefone' => '(11) 99999-3333',
                'cpf' => '12345678903',
                'nivel_acesso_id' => $nivelMotorista->id,
                'ativo' => true,
            ]
        ];

        foreach ($motoristas as $motorista) {
            // Verificar se já existe
            $existente = Usuario::where('email', $motorista['email'])->first();
            if (!$existente) {
                Usuario::create($motorista);
                $this->command->info("Motorista {$motorista['nome']} criado com sucesso!");
            } else {
                $this->command->info("Motorista {$motorista['nome']} já existe.");
            }
        }
    }
}
