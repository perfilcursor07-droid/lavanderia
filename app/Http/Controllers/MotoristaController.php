<?php

namespace App\Http\Controllers;

use App\Models\Empacotamento;
use App\Models\EmpacotamentoPeca;
use App\Models\Entrega;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MotoristaController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Buscar status
        $statusPronto = Status::where('nome', 'Pronto para motorista')->first();
        $statusTransito = Status::where('nome', 'Em Trânsito')->first();
        $statusEntregue = Status::where('nome', 'Entregue')->first();
        $statusConfirmado = Status::where('nome', 'Confirmado pelo Cliente')->first();
        
        // Contar empacotamentos
        $prontos = Empacotamento::where('status_id', $statusPronto?->id)->count();
        $emTransito = Empacotamento::where('status_id', $statusTransito?->id)->count();
        $entreguesHoje = Empacotamento::where('status_id', $statusEntregue?->id)
            ->whereDate('data_entrega', Carbon::today())
            ->count();
        $total = Empacotamento::count();
        
        // Buscar empacotamentos prontos para entrega com suas peças individuais (apenas sacolas prontas)
        $empacotamentosProntos = Empacotamento::with([
                'coleta.estabelecimento', 
                'pecasIndividuais' => function($query) {
                    $query->where('status_saida', 'pronto');
                },
                'pecasIndividuais.tipo', 
                'status', 
                'entrega'
            ])
            ->whereHas('coleta')
            ->where('status_id', $statusPronto?->id)
            ->whereHas('pecasIndividuais', function($query) {
                $query->where('status_saida', 'pronto');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Contar total de sacolas prontas (apenas as que estão efetivamente prontas)
        $totalSacolasProntas = $empacotamentosProntos->sum(function($emp) {
            return $emp->pecasIndividuais->where('status_saida', 'pronto')->count();
        });

        // Buscar empacotamentos que têm pelo menos uma sacola em trânsito
        $empacotamentosTransito = Empacotamento::with([
                'coleta.estabelecimento', 
                'pecasIndividuais' => function($query) {
                    $query->where('status_saida', 'em_transito');
                },
                'pecasIndividuais.tipo', 
                'status', 
                'entrega'
            ])
            ->whereHas('coleta')
            ->whereHas('pecasIndividuais', function($query) {
                $query->where('status_saida', 'em_transito');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Contar total de sacolas em trânsito (apenas as que estão efetivamente em trânsito)
        $totalSacolasTransito = $empacotamentosTransito->sum(function($emp) {
            return $emp->pecasIndividuais->where('status_saida', 'em_transito')->count();
        });
            
        // Buscar entregas realizadas hoje
        $empacotamentosEntregues = Empacotamento::with(['coleta.estabelecimento', 'status', 'entrega.motoristaEntrega'])
            ->whereHas('coleta')
            ->whereHas('entrega', function($query) use ($statusEntregue, $statusConfirmado) {
                $query->whereIn('status_id', [$statusEntregue?->id, $statusConfirmado?->id])
                      ->whereDate('data_entrega', Carbon::today());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Buscar peças entregues individualmente hoje (entregas parciais)
        $pecasEntreguesHoje = \App\Models\EmpacotamentoPeca::with([
                'empacotamento.coleta.estabelecimento',
                'tipo'
            ])
            ->where('status_saida', 'entregue')
            ->whereDate('data_entrega', Carbon::today())
            ->orderBy('data_entrega', 'desc')
            ->get();
        
        return view('motorista.dashboard', compact(
            'prontos', 'emTransito', 'entreguesHoje', 'total',
            'empacotamentosProntos', 'empacotamentosTransito', 'empacotamentosEntregues',
            'totalSacolasProntas', 'totalSacolasTransito', 'pecasEntreguesHoje'
        ));
    }
    
    public function buscarEmpacotamento(Request $request)
    {
        $codigo = $request->input('codigo');
        
        // Log para debug
        \Log::info("🔍 BUSCAR EMPACOTAMENTO - Código recebido: " . $codigo);
        \Log::info("🔍 Request completo: " . json_encode($request->all()));

        $empacotamento = Empacotamento::with(['coleta.estabelecimento', 'status', 'entrega'])
            ->where('codigo_qr', $codigo)
            ->first();
            
        \Log::info("🔍 Empacotamento encontrado: " . ($empacotamento ? "SIM (ID: {$empacotamento->id})" : "NÃO"));

        if (!$empacotamento) {
            \Log::warning("❌ Empacotamento não encontrado para código: " . $codigo);
            return response()->json([
                'success' => false,
                'message' => '❌ Empacotamento não encontrado!\nVerifique se o QR Code está correto.'
            ]);
        }

        // Verificar se o empacotamento está ativo
        if (!$empacotamento->coleta) {
            return response()->json([
                'success' => false,
                'message' => '❌ Empacotamento sem coleta associada!'
            ]);
        }

        \Log::info("✅ Retornando empacotamento com sucesso - ID: " . $empacotamento->id);
        
        return response()->json([
            'success' => true,
            'empacotamento' => $empacotamento->load(['coleta.estabelecimento', 'status', 'entrega'])
        ]);
    }
    
    public function confirmarSaida(Request $request)
    {
        $request->validate([
            'empacotamento_id' => 'required|exists:empacotamento,id'
        ]);

        DB::beginTransaction();
        try {
            $empacotamento = Empacotamento::findOrFail($request->empacotamento_id);

            // Verificar se está disponível para entrega
            $statusPermitidos = ['Pronto para motorista', 'Em Trânsito'];
            if (!in_array($empacotamento->status->nome, $statusPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este empacotamento não está disponível para entrega. Status atual: ' . $empacotamento->status->nome
                ]);
            }

            // Verificar se já tem entrega em andamento
            $entregaExistente = $empacotamento->entrega;
            if ($entregaExistente && $entregaExistente->motorista_saida_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este empacotamento já foi assumido por outro motorista'
                ]);
            }

            // Buscar status "Em trânsito"
            $statusTransito = Status::where('nome', 'Em trânsito')->first();
            if (!$statusTransito) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status "Em trânsito" não encontrado no sistema'
                ]);
            }

            // Atualizar status do empacotamento
            $empacotamento->update(['status_id' => $statusTransito->id]);

            // Criar ou atualizar entrega
            Entrega::updateOrCreate(
                ['empacotamento_id' => $empacotamento->id],
                [
                    'status_id' => $statusTransito->id,
                    'data_saida' => now(),
                    'motorista_saida_id' => Auth::id()
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Saída confirmada com sucesso! Empacotamento agora está em trânsito.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao confirmar saída: ' . $e->getMessage()
            ]);
        }
    }
    
    public function confirmarEntrega(Request $request)
    {
        $request->validate([
            'empacotamento_id' => 'required|exists:empacotamento,id',
            'nome_recebedor' => 'required|string|max:255',
            'assinatura_recebedor' => 'required|string'
        ]);

        DB::beginTransaction();
        try {
            $empacotamento = Empacotamento::findOrFail($request->empacotamento_id);

            // Buscar status "Confirmado pelo Cliente" - finaliza o processo imediatamente
            $statusConfirmado = Status::where('nome', 'Confirmado pelo Cliente')->first();
            
            if (!$statusConfirmado) {
                // Se não encontrar, usar "Entregue" como fallback
                $statusConfirmado = Status::where('nome', 'Entregue')->first();
            }

            // Atualizar status do empacotamento para finalizado
            $empacotamento->update(['status_id' => $statusConfirmado->id]);

            // Criar ou atualizar entrega com todos os dados finais
            Entrega::updateOrCreate(
                ['empacotamento_id' => $empacotamento->id],
                [
                    'status_id' => $statusConfirmado->id,
                    'data_entrega' => now(),
                    'data_confirmacao_recebimento' => now(), // Confirma automaticamente
                    'motorista_entrega_id' => Auth::id(),
                    'nome_recebedor' => $request->nome_recebedor,
                    'assinatura_recebedor' => $request->assinatura_recebedor,
                    'assinatura_cliente' => $request->assinatura_recebedor // Usar a mesma assinatura
                ]
            );

            // Atualizar todas as peças do empacotamento como entregues
            \App\Models\EmpacotamentoPeca::where('empacotamento_id', $empacotamento->id)
                ->where('status_saida', 'em_transito')
                ->whereNull('data_entrega')
                ->update([
                    'status_saida' => 'entregue',
                    'data_entrega' => now(),
                    'motorista_entrega_id' => Auth::id(),
                    'nome_recebedor' => $request->nome_recebedor,
                    'assinatura_recebedor' => $request->assinatura_recebedor
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ Entrega confirmada e finalizada com sucesso!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao confirmar entrega: ' . $e->getMessage()
            ]);
        }
    }

    public function confirmarRecebimento(Request $request)
    {
        $request->validate([
            'codigo_qr' => 'required|string',
            'assinatura_cliente' => 'required|string'
        ]);

        $empacotamento = Empacotamento::where('codigo_qr', $request->codigo_qr)->first();

        if (!$empacotamento) {
            return response()->json([
                'success' => false,
                'message' => 'Código QR não encontrado!'
            ]);
        }

        if ($empacotamento->status->nome !== 'Entregue') {
            return response()->json([
                'success' => false,
                'message' => 'Este empacotamento ainda não foi entregue!'
            ]);
        }

        $statusConfirmado = Status::where('nome', 'Confirmado pelo Cliente')->first();

        // Atualizar status do empacotamento
        $empacotamento->update(['status_id' => $statusConfirmado->id]);

        // Atualizar entrega
        $entrega = Entrega::where('empacotamento_id', $empacotamento->id)->first();
        if ($entrega) {
            $entrega->update([
                'status_id' => $statusConfirmado->id,
                'data_confirmacao_recebimento' => now(),
                'assinatura_cliente' => $request->assinatura_cliente
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Recebimento confirmado com sucesso!'
        ]);
    }

    /**
     * Buscar sacola individual por QR code
     */
    public function buscarSacola(Request $request)
    {
        $codigo = $request->input('codigo');
        
        // Log para debug
        \Log::info("🏷️ BUSCAR SACOLA - Código recebido: " . $codigo);
        \Log::info("🏷️ Request completo: " . json_encode($request->all()));

        $sacola = EmpacotamentoPeca::with(['empacotamento.coleta.estabelecimento', 'empacotamento.status', 'tipo'])
            ->where('codigo_qr', $codigo)
            ->first();
            
        \Log::info("🏷️ Sacola encontrada: " . ($sacola ? "SIM (ID: {$sacola->id})" : "NÃO"));

        if (!$sacola) {
            return response()->json([
                'success' => false,
                'message' => '❌ Sacola não encontrada!\nVerifique se o QR Code está correto.'
            ]);
        }

        // Verificar se a sacola pertence a um empacotamento válido
        if (!$sacola->empacotamento || !$sacola->empacotamento->coleta) {
            return response()->json([
                'success' => false,
                'message' => '❌ Sacola sem empacotamento válido!'
            ]);
        }

        // Verificar se a sacola individual está disponível para saída
        if ($sacola->status_saida === 'em_transito') {
            return response()->json([
                'success' => false,
                'message' => '❌ Esta sacola já está em trânsito!'
            ]);
        }

        if ($sacola->status_saida === 'entregue') {
            return response()->json([
                'success' => false,
                'message' => '❌ Esta sacola já foi entregue!'
            ]);
        }

        // Verificar se empacotamento permite saída
        $statusPermitidos = ['Pronto para motorista', 'Em Trânsito'];
        if (!in_array($sacola->empacotamento->status->nome, $statusPermitidos)) {
            return response()->json([
                'success' => false,
                'message' => '❌ Este empacotamento não está disponível para saída!\nStatus atual: ' . $sacola->empacotamento->status->nome
            ]);
        }

        return response()->json([
            'success' => true,
            'sacola' => $sacola->load(['empacotamento.coleta.estabelecimento', 'empacotamento.status', 'tipo'])
        ]);
    }

    /**
     * Confirmar saída de sacola individual
     */
    public function confirmarSaidaSacola(Request $request)
    {
        \Log::info('🚚 CONFIRMAR SAÍDA SACOLA INICIADA', [
            'codigo_qr' => $request->codigo_qr,
            'usuario_id' => Auth::id()
        ]);

        $request->validate([
            'codigo_qr' => 'required|string'
        ]);

        $sacola = EmpacotamentoPeca::with(['empacotamento.status'])
            ->where('codigo_qr', $request->codigo_qr)
            ->first();

        if (!$sacola) {
            \Log::warning('❌ Sacola não encontrada!', ['codigo_qr' => $request->codigo_qr]);
            return response()->json([
                'success' => false,
                'message' => 'Sacola não encontrada!'
            ]);
        }

        \Log::info('📦 Sacola encontrada', [
            'sacola_id' => $sacola->id,
            'status_atual' => $sacola->status_saida,
            'empacotamento_id' => $sacola->empacotamento->id,
            'empacotamento_status' => $sacola->empacotamento->status->nome
        ]);

        // Verificar se a sacola individual pode dar saída
        if ($sacola->status_saida === 'em_transito') {
            \Log::warning('⚠️ Sacola já está em trânsito', [
                'status_sacola' => $sacola->status_saida,
                'codigo_qr' => $sacola->codigo_qr
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Esta sacola já está em trânsito!'
            ]);
        }

        if ($sacola->status_saida === 'entregue') {
            \Log::warning('⚠️ Sacola já foi entregue', [
                'status_sacola' => $sacola->status_saida,
                'codigo_qr' => $sacola->codigo_qr
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Esta sacola já foi entregue!'
            ]);
        }

        // Verificar se empacotamento permite saída (deve estar pelo menos "Pronto" ou "Em Trânsito")
        $statusPermitidos = ['Pronto para motorista', 'Em Trânsito'];
        if (!in_array($sacola->empacotamento->status->nome, $statusPermitidos)) {
            \Log::warning('⚠️ Empacotamento não permite saída', [
                'status_empacotamento' => $sacola->empacotamento->status->nome,
                'status_permitidos' => $statusPermitidos
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Este empacotamento não está disponível para saída!'
            ]);
        }

        $statusTransito = Status::where('nome', 'Em Trânsito')->first();
        
        if (!$statusTransito) {
            \Log::error('❌ Status "Em Trânsito" não encontrado!');
            return response()->json([
                'success' => false,
                'message' => 'Erro no sistema: Status não encontrado!'
            ]);
        }

        \Log::info('🔄 Atualizando status da sacola', [
            'de' => $sacola->status_saida,
            'para' => 'em_transito'
        ]);

        // Atualizar status da sacola individual
        $atualizado = $sacola->update([
            'status_saida' => 'em_transito',
            'data_saida' => now(),
            'motorista_saida_id' => Auth::id()
        ]);

        if (!$atualizado) {
            \Log::error('❌ Falha ao atualizar sacola!');
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar sacola!'
            ]);
        }

        // Recarregar sacola para verificar se foi atualizada
        $sacola->refresh();
        \Log::info('✅ Status da sacola atualizado', [
            'novo_status' => $sacola->status_saida,
            'data_saida' => $sacola->data_saida
        ]);

        // Verificar se todas as sacolas do empacotamento saíram
        $todasSacolas = $sacola->empacotamento->pecasIndividuais;
        $sacolasEmTransito = $todasSacolas->where('status_saida', 'em_transito');

        \Log::info('📊 Verificando outras sacolas', [
            'total_sacolas' => $todasSacolas->count(),
            'em_transito' => $sacolasEmTransito->count()
        ]);

        if ($todasSacolas->count() === $sacolasEmTransito->count()) {
            \Log::info('🎉 Todas as sacolas em trânsito! Atualizando empacotamento...');
            
            // Todas as sacolas saíram, atualizar status do empacotamento
            $sacola->empacotamento->update(['status_id' => $statusTransito->id]);

            // Criar ou atualizar entrega
            Entrega::updateOrCreate(
                ['empacotamento_id' => $sacola->empacotamento->id],
                [
                    'status_id' => $statusTransito->id,
                    'data_saida' => now(),
                    'motorista_saida_id' => Auth::id()
                ]
            );

            $mensagem = 'Sacola confirmada! 🎉 TODAS as sacolas do empacotamento estão agora em trânsito.';
        } else {
            $restantes = $todasSacolas->count() - $sacolasEmTransito->count();
            $mensagem = "Sacola confirmada! ✅ Ainda restam {$restantes} sacola(s) para saída.";
        }

        \Log::info('✅ CONFIRMAÇÃO CONCLUÍDA', [
            'mensagem' => $mensagem,
            'todas_em_transito' => $todasSacolas->count() === $sacolasEmTransito->count()
        ]);

        return response()->json([
            'success' => true,
            'message' => $mensagem,
            'todas_sacolasem_transito' => $todasSacolas->count() === $sacolasEmTransito->count()
        ]);
    }

    /**
     * Validar QR Code na entrega - verificar se pertence ao hotel correto
     */
    public function validarQREntrega(Request $request)
    {
        $request->validate([
            'codigo_qr' => 'required|string',
            'estabelecimento_id' => 'required|integer'
        ]);

        // Buscar pela peça individual
        $peca = EmpacotamentoPeca::with(['empacotamento.coleta.estabelecimento', 'tipo'])
            ->where('codigo_qr', $request->codigo_qr)
            ->first();

        if (!$peca) {
            return response()->json([
                'success' => false,
                'message' => '❌ QR Code não encontrado!',
                'type' => 'error'
            ]);
        }

        // Verificar se pertence ao estabelecimento correto
        if ($peca->empacotamento->coleta->estabelecimento_id != $request->estabelecimento_id) {
            return response()->json([
                'success' => false,
                'message' => '🚨 ATENÇÃO! Esta peça NÃO pertence a este hotel!',
                'details' => [
                    'hotel_correto' => $peca->empacotamento->coleta->estabelecimento->nome_fantasia,
                    'codigo_coleta' => $peca->empacotamento->coleta->numero_coleta
                ],
                'type' => 'wrong_hotel'
            ]);
        }

        // Verificar se já foi entregue
        if ($peca->status_saida === 'entregue') {
            return response()->json([
                'success' => false,
                'message' => '⚠️ Esta peça já foi entregue anteriormente!',
                'type' => 'already_delivered'
            ]);
        }

        // Verificar se está em trânsito (pode ser entregue)
        if ($peca->status_saida !== 'em_transito') {
            return response()->json([
                'success' => false,
                'message' => '⚠️ Esta peça não está em trânsito!',
                'type' => 'not_in_transit'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => '✅ QR Code válido para entrega!',
            'peca' => [
                'id' => $peca->id,
                'codigo_qr' => $peca->codigo_qr,
                'tipo' => $peca->tipo->nome,
                'quantidade' => $peca->quantidade,
                'hotel' => $peca->empacotamento->coleta->estabelecimento->nome_fantasia,
                'coleta' => $peca->empacotamento->coleta->numero_coleta,
                'relave' => $peca->relave,
                'inutilizada' => $peca->inutilizada
            ],
            'type' => 'valid'
        ]);
    }

    /**
     * Finalizar carregamento do motorista com feedback visual
     */
    public function finalizarCarregamento(Request $request)
    {
        $request->validate([
            'empacotamento_id' => 'required|exists:empacotamento,id'
        ]);

        $empacotamento = Empacotamento::with(['pecasIndividuais', 'coleta.estabelecimento'])->findOrFail($request->empacotamento_id);
        
        // Verificar se todas as peças necessárias foram carregadas
        $pecasEmTransito = $empacotamento->pecasIndividuais()->where('status_saida', 'em_transito')->count();
        $pecasProntas = $empacotamento->pecasIndividuais()->where('status_saida', 'pronto')->count();
        
        if ($pecasProntas > 0) {
            return response()->json([
                'success' => false,
                'message' => "⚠️ Ainda há {$pecasProntas} peça(s) não carregada(s)!",
                'type' => 'incomplete'
            ]);
        }

        if ($pecasEmTransito === 0) {
            return response()->json([
                'success' => false,
                'message' => '⚠️ Nenhuma peça foi carregada!',
                'type' => 'empty'
            ]);
        }

        DB::beginTransaction();
        try {
            // Atualizar status do empacotamento para "Em Trânsito"
            $statusTransito = Status::where('nome', 'Em Trânsito')->first();
            $empacotamento->update([
                'status_id' => $statusTransito->id,
                'data_saida' => now(),
                'motorista_saida_id' => Auth::id()
            ]);

            // Criar ou atualizar registro de entrega
            Entrega::updateOrCreate(
                ['empacotamento_id' => $empacotamento->id],
                [
                    'status_id' => $statusTransito->id,
                    'data_saida' => now(),
                    'motorista_saida_id' => Auth::id()
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "🎉 CARREGAMENTO CONCLUÍDO!\n{$pecasEmTransito} peça(s) carregada(s) para {$empacotamento->coleta->estabelecimento->nome_fantasia}",
                'stats' => [
                    'pecas_carregadas' => $pecasEmTransito,
                    'hotel' => $empacotamento->coleta->estabelecimento->nome_fantasia,
                    'data_saida' => now()->format('H:i')
                ],
                'type' => 'completed'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erro ao finalizar carregamento:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao finalizar carregamento: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Confirmar entrega de peça individual com validação
     */
    public function confirmarEntregaPeca(Request $request)
    {
        $request->validate([
            'codigo_qr' => 'required|string',
            'estabelecimento_id' => 'required|integer'
        ]);

        // Primeiro validar o QR code
        $validacao = $this->validarQREntrega($request);
        $responseData = json_decode($validacao->getContent(), true);

        if (!$responseData['success']) {
            return $validacao; // Retornar erro de validação
        }

        // Buscar a peça
        $peca = EmpacotamentoPeca::where('codigo_qr', $request->codigo_qr)->first();

        DB::beginTransaction();
        try {
            // Marcar peça como entregue
            $peca->update([
                'status_saida' => 'entregue',
                'data_entrega' => now(),
                'motorista_entrega_id' => Auth::id()
            ]);

            // Verificar se todas as peças do empacotamento foram entregues
            $empacotamento = $peca->empacotamento;
            $pecasRestantes = $empacotamento->pecasIndividuais()
                ->whereIn('status_saida', ['pronto', 'em_transito'])
                ->count();

            $mensagem = "✅ Peça entregue com sucesso!";
            
            if ($pecasRestantes === 0) {
                // Todas as peças foram entregues, atualizar status do empacotamento
                $statusEntregue = Status::where('nome', 'Entregue')->first();
                $empacotamento->update([
                    'status_id' => $statusEntregue->id,
                    'data_entrega' => now(),
                    'motorista_entrega_id' => Auth::id()
                ]);

                // Atualizar entrega
                Entrega::where('empacotamento_id', $empacotamento->id)->update([
                    'status_id' => $statusEntregue->id,
                    'data_entrega' => now(),
                    'motorista_entrega_id' => Auth::id()
                ]);

                $mensagem = "🎉 EMPACOTAMENTO TOTALMENTE ENTREGUE!\nTodas as peças foram entregues ao cliente.";
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $mensagem,
                'stats' => [
                    'pecas_restantes' => $pecasRestantes,
                    'empacotamento_completo' => $pecasRestantes === 0
                ],
                'type' => $pecasRestantes === 0 ? 'completed' : 'partial'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erro ao confirmar entrega de peça:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao confirmar entrega: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Confirmar saída de todas as sacolas de um empacotamento de uma vez
     */
    public function confirmarTodasSacolas(Request $request)
    {
        $request->validate([
            'empacotamento_id' => 'required|exists:empacotamento,id'
        ]);

        DB::beginTransaction();
        try {
            $empacotamento = Empacotamento::with(['pecasIndividuais', 'coleta.estabelecimento'])->findOrFail($request->empacotamento_id);

            // Verificar se está disponível para saída
            $statusPermitidos = ['Pronto para motorista'];
            if (!in_array($empacotamento->status->nome, $statusPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este empacotamento não está disponível para saída. Status atual: ' . $empacotamento->status->nome
                ]);
            }

            // Buscar status "Em trânsito"
            $statusTransito = Status::where('nome', 'Em trânsito')->first();
            if (!$statusTransito) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status "Em trânsito" não encontrado no sistema'
                ]);
            }

            // Contar sacolas disponíveis para saída (status_saida = 'pronto')
            $sacolasProntas = $empacotamento->pecasIndividuais()->where('status_saida', 'pronto')->count();
            
            if ($sacolasProntas === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não há sacolas prontas para saída neste empacotamento!'
                ]);
            }

            // Atualizar todas as sacolas prontas para "em_transito"
            $sacolasAtualizadas = $empacotamento->pecasIndividuais()
                ->where('status_saida', 'pronto')
                ->update([
                    'status_saida' => 'em_transito',
                    'data_saida' => now(),
                    'motorista_saida_id' => Auth::id()
                ]);

            // Atualizar status do empacotamento para "Em Trânsito"
            $empacotamento->update(['status_id' => $statusTransito->id]);

            // Criar ou atualizar entrega
            Entrega::updateOrCreate(
                ['empacotamento_id' => $empacotamento->id],
                [
                    'status_id' => $statusTransito->id,
                    'data_saida' => now(),
                    'motorista_saida_id' => Auth::id()
                ]
            );

            DB::commit();

            $nomeEstabelecimento = $empacotamento->coleta->estabelecimento->nome_fantasia ?? $empacotamento->coleta->estabelecimento->razao_social ?? 'Estabelecimento';
            
            return response()->json([
                'success' => true,
                'message' => "🎉 TODAS AS SACOLAS CONFIRMADAS!\n\n✅ {$sacolasAtualizadas} sacola(s) estão agora em trânsito\n🏢 Destino: {$nomeEstabelecimento}\n\n🚚 O empacotamento completo foi movido para 'Sacolas em Trânsito'.",
                'sacolas_confirmadas' => $sacolasAtualizadas,
                'estabelecimento' => $nomeEstabelecimento
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erro ao confirmar todas as sacolas:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao confirmar saída: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obter estatísticas em tempo real para o motorista
     */
    public function getEstatisticasMotorista()
    {
        $motorista = Auth::user();
        
        // Estatísticas do dia
        $hoje = Carbon::today();
        
        $stats = [
            'carregamentos_hoje' => Entrega::where('motorista_saida_id', $motorista->id)
                ->whereDate('data_saida', $hoje)
                ->count(),
                
            'entregas_hoje' => Entrega::where('motorista_entrega_id', $motorista->id)
                ->whereDate('data_entrega', $hoje)
                ->count(),
                
            'pecas_carregadas_hoje' => EmpacotamentoPeca::where('motorista_saida_id', $motorista->id)
                ->whereDate('data_saida', $hoje)
                ->count(),
                
            'pecas_entregues_hoje' => EmpacotamentoPeca::where('motorista_entrega_id', $motorista->id)
                ->whereDate('data_entrega', $hoje)
                ->count(),
                
            'empacotamentos_prontos' => Empacotamento::whereHas('status', function($q) {
                    $q->where('nome', 'Pronto para motorista');
                })
                ->whereHas('pecasIndividuais', function($q) {
                    $q->where('status_saida', 'pronto');
                })
                ->count(),
                
            'empacotamentos_transito' => Empacotamento::whereHas('status', function($q) {
                    $q->where('nome', 'Em Trânsito');
                })
                ->whereHas('pecasIndividuais', function($q) {
                    $q->where('status_saida', 'em_transito');
                })
                ->count()
        ];
        
        return response()->json(['stats' => $stats]);
    }

    /**
     * Listar estabelecimentos para validação de entrega
     */
    public function getEstabelecimentos()
    {
        $estabelecimentos = \App\Models\Estabelecimento::where('ativo', true)
            ->orderBy('nome_fantasia')
            ->get(['id', 'nome_fantasia', 'razao_social']);
            
        return response()->json(['estabelecimentos' => $estabelecimentos]);
    }
}
