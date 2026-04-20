<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coleta;
use App\Models\Estabelecimento;
use App\Models\Status;
use App\Models\Usuario;
use Carbon\Carbon;

class ColetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar dados necessários
        $estabelecimentos = Estabelecimento::where('ativo', true)->get();
        $statusConcluida = Status::where('nome', 'Concluída')->first();
        $operador = Usuario::whereHas('nivelAcesso', function($q) {
            $q->whereIn('nome', ['Gestor', 'Pesagem']);
        })->first();

        if (!$estabelecimentos->count() || !$statusConcluida || !$operador) {
            $this->command->error('Dados necessários não encontrados. Execute os seeders de estabelecimentos, status e usuários primeiro.');
            return;
        }

        // Criar coletas de exemplo
        foreach ($estabelecimentos->take(3) as $index => $estabelecimento) {
            $coleta = Coleta::create([
                'estabelecimento_id' => $estabelecimento->id,
                'usuario_id' => $operador->id,
                'status_id' => $statusConcluida->id,
                'data_agendamento' => Carbon::now()->subDays($index + 1),
                'data_coleta' => Carbon::now()->subDays($index),
                'data_conclusao' => Carbon::now()->subDays($index),
                'numero_coleta' => 'COL' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                'observacoes' => "Coleta de teste para {$estabelecimento->nome_fantasia}"
            ]);

            $this->command->info("Coleta criada: {$coleta->id} para {$estabelecimento->nome_fantasia}");
        }
    }
}
