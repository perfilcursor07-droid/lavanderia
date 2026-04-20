<?php

namespace App\Http\Controllers;

use App\Models\Empacotamento;
use App\Models\Entrega;
use App\Models\Status;
use Illuminate\Http\Request;

class ConfirmacaoClienteController extends Controller
{
    /**
     * Página de confirmação de recebimento pelo cliente
     */
    public function index(Request $request)
    {
        $codigoQr = $request->get('codigo');
        $empacotamento = null;
        
        if ($codigoQr) {
            $empacotamento = Empacotamento::with(['coleta.estabelecimento', 'status', 'entrega'])
                ->where('codigo_qr', $codigoQr)
                ->first();
        }
        
        return view('confirmacao-cliente.index', compact('empacotamento', 'codigoQr'));
    }
    
    /**
     * Confirmar recebimento pelo cliente
     */
    public function confirmar(Request $request)
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
        
        if ($empacotamento->status->nome === 'Confirmado pelo Cliente') {
            return response()->json([
                'success' => false,
                'message' => 'Este empacotamento já foi confirmado!'
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
}
