<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tipo;

class TiposSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            // Roupa de cama
            [
                'nome' => 'Lençol Solteiro',
                'descricao' => 'Lençol para cama de solteiro',
                'categoria' => 'roupa_cama',
                'ativo' => true
            ],
            [
                'nome' => 'Lençol Casal',
                'descricao' => 'Lençol para cama de casal',
                'categoria' => 'roupa_cama',
                'ativo' => true
            ],
            [
                'nome' => 'Fronha',
                'descricao' => 'Fronha para travesseiro',
                'categoria' => 'roupa_cama',
                'ativo' => true
            ],
            [
                'nome' => 'Edredom',
                'descricao' => 'Edredom/cobertor',
                'categoria' => 'roupa_cama',
                'ativo' => true
            ],
            [
                'nome' => 'Colcha',
                'descricao' => 'Colcha de cama',
                'categoria' => 'roupa_cama',
                'ativo' => true
            ],

            // Roupa de banho
            [
                'nome' => 'Toalha de Banho',
                'descricao' => 'Toalha de banho grande',
                'categoria' => 'roupa_banho',
                'ativo' => true
            ],
            [
                'nome' => 'Toalha de Rosto',
                'descricao' => 'Toalha de rosto pequena',
                'categoria' => 'roupa_banho',
                'ativo' => true
            ],
            [
                'nome' => 'Roupão',
                'descricao' => 'Roupão de banho',
                'categoria' => 'roupa_banho',
                'ativo' => true
            ],

            // Vestuário
            [
                'nome' => 'Camisa',
                'descricao' => 'Camisa social ou casual',
                'categoria' => 'vestuario',
                'ativo' => true
            ],
            [
                'nome' => 'Calça',
                'descricao' => 'Calça social ou casual',
                'categoria' => 'vestuario',
                'ativo' => true
            ],
            [
                'nome' => 'Vestido',
                'descricao' => 'Vestido feminino',
                'categoria' => 'vestuario',
                'ativo' => true
            ],
            [
                'nome' => 'Terno/Blazer',
                'descricao' => 'Terno completo ou blazer',
                'categoria' => 'vestuario',
                'ativo' => true
            ],

            // Mesa e copa
            [
                'nome' => 'Toalha de Mesa',
                'descricao' => 'Toalha de mesa',
                'categoria' => 'mesa_copa',
                'ativo' => true
            ],
            [
                'nome' => 'Guardanapo',
                'descricao' => 'Guardanapo de tecido',
                'categoria' => 'mesa_copa',
                'ativo' => true
            ],

            // Cortinas
            [
                'nome' => 'Cortina',
                'descricao' => 'Cortina de ambiente',
                'categoria' => 'cortina',
                'ativo' => true
            ]
        ];

        foreach ($tipos as $tipo) {
            Tipo::updateOrCreate(
                ['nome' => $tipo['nome']], // Busca por nome
                $tipo // Atualiza ou cria com esses dados
            );
        }
    }
}
