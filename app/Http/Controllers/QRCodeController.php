<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empacotamento;
use App\Models\EmpacotamentoPeca;

class QRCodeController extends Controller
{
    /**
     * Rastrear empacotamento por QR Code
     */
    public function rastrear($codigo)
    {
        // Tentar encontrar como empacotamento
        $empacotamento = Empacotamento::where('codigo_qr', $codigo)
                                    ->with(['coleta.estabelecimento', 'pecasIndividuais.tipo', 'status', 'entrega', 'usuarioEmpacotamento'])
                                    ->first();

        if ($empacotamento) {
            return view('qrcodes.rastrear', compact('empacotamento'));
        }

        // Tentar encontrar como peça individual
        $empacotamentoPeca = EmpacotamentoPeca::where('codigo_qr', $codigo)
                                           ->with([
                                               'empacotamento.coleta.estabelecimento',
                                               'empacotamento.usuarioEmpacotamento',
                                               'empacotamento.status',
                                               'tipo'
                                           ])
                                           ->first();

        if ($empacotamentoPeca) {
            return view('qrcodes.rastrear-peca', compact('empacotamentoPeca'));
        }

        return view('qrcodes.nao-encontrado', compact('codigo'));
    }

    /**
     * Rastrear peça individual por QR Code
     */
    public function rastrearPeca($codigo)
    {
        $empacotamentoPeca = EmpacotamentoPeca::where('codigo_qr', $codigo)
                                           ->with([
                                               'empacotamento.coleta.estabelecimento',
                                               'empacotamento.usuarioEmpacotamento',
                                               'empacotamento.status',
                                               'tipo'
                                           ])
                                           ->first();

        if (!$empacotamentoPeca) {
            return view('qrcodes.nao-encontrado', compact('codigo'));
        }

        return view('qrcodes.rastrear-peca', compact('empacotamentoPeca'));
    }

    /**
     * Gerar QR Code para empacotamento
     */
    public function gerar($empacotamento_id)
    {
        $empacotamento = Empacotamento::findOrFail($empacotamento_id);

        // Aqui você pode implementar a lógica de geração do QR Code
        // Por exemplo, usando uma biblioteca como SimpleSoftwareIO/simple-qrcode

        return response()->json([
            'success' => true,
            'codigo' => $empacotamento->codigo_qr,
            'url' => route('qrcodes.rastrear', $empacotamento->codigo_qr)
        ]);
    }
}
