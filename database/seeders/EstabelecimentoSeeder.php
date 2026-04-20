<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Estabelecimento;

class EstabelecimentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estabelecimentos = [
            [
                'cnpj' => '11222333000144',
                'razao_social' => 'Hotel Exemplo Ltda',
                'nome_fantasia' => 'Hotel Exemplo',
                'endereco' => 'Rua das Flores',
                'numero' => '123',
                'complemento' => 'Andar 1',
                'bairro' => 'Centro',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'cep' => '01234-567',
                'telefone' => '(11) 99999-9999',
                'emails' => json_encode(['contato@hotelexemplo.com.br']),
                'contatos_responsaveis' => json_encode(['João Silva']),
                'observacoes' => 'Cliente VIP - prioridade nas coletas',
                'ativo' => true,
            ],
            [
                'cnpj' => '22333444000155',
                'razao_social' => 'Pousada Beira Mar S/A',
                'nome_fantasia' => 'Pousada Beira Mar',
                'endereco' => 'Avenida Atlântica',
                'numero' => '456',
                'complemento' => null,
                'bairro' => 'Copacabana',
                'cidade' => 'Rio de Janeiro',
                'estado' => 'RJ',
                'cep' => '22070-011',
                'telefone' => '(21) 88888-8888',
                'emails' => json_encode(['reservas@pousadabeiramar.com.br']),
                'contatos_responsaveis' => json_encode(['Maria Santos']),
                'observacoes' => 'Coletas diárias - horário preferencial: 14h às 16h',
                'ativo' => true,
            ],
            [
                'cnpj' => '33444555000166',
                'razao_social' => 'Resort Tropical Eireli',
                'nome_fantasia' => 'Resort Tropical',
                'endereco' => 'Estrada da Praia',
                'numero' => '789',
                'complemento' => 'Km 15',
                'bairro' => 'Praia do Forte',
                'cidade' => 'Mata de São João',
                'estado' => 'BA',
                'cep' => '48280-000',
                'telefone' => '(71) 77777-7777',
                'emails' => json_encode(['operacoes@resorttropical.com.br', 'gerencia@resorttropical.com.br']),
                'contatos_responsaveis' => json_encode(['Carlos Oliveira', 'Ana Gerente']),
                'observacoes' => 'Grande volume - necessário caminhão para coleta',
                'ativo' => true,
            ],
            [
                'cnpj' => '44555666000177',
                'razao_social' => 'Hotel Executivo Ltda ME',
                'nome_fantasia' => 'Hotel Executivo',
                'endereco' => 'Rua dos Negócios',
                'numero' => '321',
                'complemento' => 'Sala 101',
                'bairro' => 'Funcionários',
                'cidade' => 'Belo Horizonte',
                'estado' => 'MG',
                'cep' => '30112-000',
                'telefone' => '(31) 66666-6666',
                'emails' => json_encode(['gerencia@hotelexecutivo.com.br']),
                'contatos_responsaveis' => json_encode(['Ana Costa']),
                'observacoes' => 'Foco em roupas executivas - cuidado especial com ternos',
                'ativo' => true,
            ],
            [
                'cnpj' => '55666777000188',
                'razao_social' => 'Motel Descanso Ltda',
                'nome_fantasia' => 'Motel Descanso',
                'endereco' => 'Rodovia BR-101',
                'numero' => '1500',
                'complemento' => 'Km 25',
                'bairro' => 'Zona Rural',
                'cidade' => 'Curitiba',
                'estado' => 'PR',
                'cep' => '82000-000',
                'telefone' => '(41) 55555-5555',
                'emails' => null,
                'contatos_responsaveis' => json_encode(['Pedro Souza']),
                'observacoes' => 'Coletas noturnas preferenciais',
                'ativo' => false,
            ],
        ];

        foreach ($estabelecimentos as $estabelecimento) {
            Estabelecimento::create($estabelecimento);
        }
    }
}
