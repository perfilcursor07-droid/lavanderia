<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empacotamento;
use App\Models\Coleta;
use App\Models\Status;
use App\Models\Usuario;
use Carbon\Carbon;

class EmpacotamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar dados necessários
        $coletas = Coleta::all();
        $statusPronto = Status::where('nome', 'Pronto para Entrega')->first();
        $statusTransito = Status::where('nome', 'Em Trânsito')->first();
        $statusEntregue = Status::where('nome', 'Entregue')->first();
        $operador = Usuario::whereHas('nivelAcesso', function($q) {
            $q->whereIn('nome', ['Gestor', 'Empacotamento']);
        })->first();
        $motorista = Usuario::whereHas('nivelAcesso', function($q) {
            $q->where('nome', 'Motorista');
        })->first();

        if (!$coletas->count() || !$statusPronto || !$operador) {
            $this->command->error('Dados necessários não encontrados. Execute os seeders de coletas, status e usuários primeiro.');
            return;
        }

        // Criar empacotamentos de exemplo
        $empacotamentos = [
            [
                'coleta_id' => $coletas->first()->id,
                'usuario_empacotamento_id' => $operador->id,
                'motorista_id' => $motorista?->id,
                'status_id' => $statusPronto->id,
                'data_empacotamento' => Carbon::now()->subHours(2),
                'observacoes_empacotamento' => 'Empacotamento teste - pronto para entrega'
            ],
            [
                'coleta_id' => $coletas->skip(1)->first()?->id ?? $coletas->first()->id,
                'usuario_empacotamento_id' => $operador->id,
                'motorista_id' => $motorista?->id,
                'motorista_saida_id' => $motorista?->id,
                'status_id' => $statusTransito?->id ?? $statusPronto->id,
                'data_empacotamento' => Carbon::now()->subHours(4),
                'data_saida' => Carbon::now()->subHour(),
                'observacoes_empacotamento' => 'Empacotamento teste - em trânsito'
            ]
        ];

        if ($statusEntregue && $motorista) {
            $empacotamentos[] = [
                'coleta_id' => $coletas->skip(2)->first()?->id ?? $coletas->first()->id,
                'usuario_empacotamento_id' => $operador->id,
                'motorista_id' => $motorista->id,
                'motorista_saida_id' => $motorista->id,
                'motorista_entrega_id' => $motorista->id,
                'status_id' => $statusEntregue->id,
                'data_empacotamento' => Carbon::now()->subHours(6),
                'data_saida' => Carbon::now()->subHours(3),
                'data_entrega' => Carbon::now()->subHour(),
                'nome_recebedor' => 'João da Silva',
                'assinatura_recebedor' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==',
                'observacoes_empacotamento' => 'Empacotamento teste - entregue hoje'
            ];
        }

        foreach ($empacotamentos as $empacotamento) {
            if ($empacotamento['coleta_id']) {
                Empacotamento::create($empacotamento);
                $this->command->info("Empacotamento criado para coleta ID: {$empacotamento['coleta_id']}");
            }
        }
    }
}
