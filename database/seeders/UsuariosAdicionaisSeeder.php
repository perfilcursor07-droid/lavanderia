<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\NivelAcesso;
use Illuminate\Support\Facades\Hash;

class UsuariosAdicionaisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar os níveis de acesso
        $nivelGestor = NivelAcesso::where('nome', 'Gestor')->first();
        $nivelPesagem = NivelAcesso::where('nome', 'Pesagem')->first();
        $nivelEmpacotamento = NivelAcesso::where('nome', 'Empacotamento')->first();
        $nivelMotorista = NivelAcesso::where('nome', 'Motorista')->first();

        // Criar usuários gestores
        $gestores = [
            [
                'nome' => 'Ana Costa',
                'email' => 'ana.gestor@lavanderia.com',
                'password' => Hash::make('123456'),
                'telefone' => '(11) 98888-1111',
                'cpf' => '11111111111',
                'nivel_acesso_id' => $nivelGestor->id,
                'ativo' => true,
            ],
            [
                'nome' => 'Roberto Gerente',
                'email' => 'roberto.gestor@lavanderia.com',
                'password' => Hash::make('123456'),
                'telefone' => '(11) 97777-1111',
                'cpf' => '33333333333',
                'nivel_acesso_id' => $nivelGestor->id,
                'ativo' => true,
            ]
        ];

        // Criar usuários de pesagem
        $pesagem = [
            [
                'nome' => 'Pedro Almeida',
                'email' => 'pedro.pesagem@lavanderia.com',
                'password' => Hash::make('123456'),
                'telefone' => '(11) 98888-2222',
                'cpf' => '22222222222',
                'nivel_acesso_id' => $nivelPesagem->id,
                'ativo' => true,
            ]
        ];

        // Criar usuários de empacotamento
        $empacotamento = [
            [
                'nome' => 'Mariana Silva',
                'email' => 'mariana.empacotamento@lavanderia.com',
                'password' => Hash::make('123456'),
                'telefone' => '(11) 98888-3333',
                'cpf' => '66666666666',
                'nivel_acesso_id' => $nivelEmpacotamento->id,
                'ativo' => true,
            ]
        ];

        // Motoristas adicionais
        $motoristasAdicionais = [
            [
                'nome' => 'Lucas Pereira',
                'email' => 'lucas.motorista@lavanderia.com',
                'password' => Hash::make('123456'),
                'telefone' => '(11) 96666-1111',
                'cpf' => '44444444444',
                'nivel_acesso_id' => $nivelMotorista->id,
                'ativo' => true,
            ],
            [
                'nome' => 'Rafael Souza',
                'email' => 'rafael.motorista@lavanderia.com',
                'password' => Hash::make('123456'),
                'telefone' => '(11) 96666-2222',
                'cpf' => '55555555555',
                'nivel_acesso_id' => $nivelMotorista->id,
                'ativo' => true,
            ]
        ];

        // Inserir todos os usuários
        $todosUsuarios = array_merge($gestores, $pesagem, $empacotamento, $motoristasAdicionais);

        foreach ($todosUsuarios as $usuario) {
            // Verificar se já existe
            $existente = Usuario::where('email', $usuario['email'])->first();
            if (!$existente) {
                Usuario::create($usuario);
                $this->command->info("Usuário {$usuario['nome']} criado com sucesso!");
            } else {
                $this->command->info("Usuário {$usuario['nome']} já existe.");
            }
        }
    }
}
