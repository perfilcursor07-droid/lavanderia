<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PainelController;
use App\Http\Controllers\EstabelecimentoController;
use App\Http\Controllers\ColetaController;
use App\Http\Controllers\AnotacaoController;
use App\Http\Controllers\PesagemController;
use App\Http\Controllers\EmpacotamentoController;
use App\Http\Controllers\MotoristaController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AcompanhamentoPublicoController;
use App\Http\Controllers\TipoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rota inicial - redireciona para acompanhamento público
Route::get('/', [AcompanhamentoPublicoController::class, 'index'])->name('home');

// Rotas públicas para acompanhamento de coletas
Route::prefix('acompanhamento')->name('acompanhamento.')->middleware('web')->group(function () {
    Route::get('/', [AcompanhamentoPublicoController::class, 'index'])->name('index');
    Route::post('/buscar', [AcompanhamentoPublicoController::class, 'buscar'])->name('buscar');
    Route::get('/buscar', function () {
        return redirect()->route('acompanhamento.index')->with('error', 'Use o formulário para fazer a busca.');
    })->name('buscar.redirect');
    Route::get('/detalhes/{id}', [AcompanhamentoPublicoController::class, 'detalhes'])->name('detalhes');
});

// Rota para acesso ao sistema (login)
Route::get('/sistema', function () {
    return redirect()->route('login');
})->name('sistema');

// Rotas públicas para confirmação do cliente
Route::get('/confirmar-recebimento', [App\Http\Controllers\ConfirmacaoClienteController::class, 'index'])->name('confirmacao-cliente.index');
Route::post('/confirmar-recebimento', [App\Http\Controllers\ConfirmacaoClienteController::class, 'confirmar'])->name('confirmacao-cliente.confirmar');

// Rota pública para rastreamento
Route::get('/rastreamento/{codigo}', [App\Http\Controllers\RastreamentoController::class, 'index'])->name('rastreamento.index');

// Rotas de autenticação (sistema interno)
Route::prefix('sistema')->group(function () {
            Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    });
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logoutGet'])->name('logout.redirect');

// Rotas protegidas por autenticação
Route::middleware(['auth', 'ensure.redirects'])->group(function () {
    
    // Dashboard/Painel
    Route::get('/painel', [PainelController::class, 'index'])->name('painel');
    
    // Acompanhar Coletas - Página dedicada
    Route::get('/acompanhar-coletas', [PainelController::class, 'acompanharColetas'])->name('acompanhar-coletas');
    Route::post('/acompanhar-coleta', [PainelController::class, 'acompanharColeta'])->name('acompanhar-coleta');
    
    // Estabelecimentos
    Route::prefix('estabelecimentos')->name('estabelecimentos.')->middleware(['nivel.acesso:estabelecimentos.visualizar'])->group(function () {
        Route::get('/', [EstabelecimentoController::class, 'index'])->name('index');
        Route::get('/cadastro', [EstabelecimentoController::class, 'create'])->middleware(['nivel.acesso:estabelecimentos.criar'])->name('create');
        Route::post('/cadastro', [EstabelecimentoController::class, 'store'])->middleware(['nivel.acesso:estabelecimentos.criar'])->name('store');
        Route::get('/buscar-cnpj', [EstabelecimentoController::class, 'buscarCnpj'])->name('buscar-cnpj');
        Route::get('/{id}', [EstabelecimentoController::class, 'show'])->name('show');
        Route::get('/{id}/editar', [EstabelecimentoController::class, 'edit'])->middleware(['nivel.acesso:estabelecimentos.editar'])->name('edit');
        Route::put('/{id}', [EstabelecimentoController::class, 'update'])->middleware(['nivel.acesso:estabelecimentos.editar'])->name('update');
        Route::delete('/{id}', [EstabelecimentoController::class, 'destroy'])->middleware(['nivel.acesso:estabelecimentos.excluir'])->name('destroy');
        Route::post('/{id}/toggle-status', [EstabelecimentoController::class, 'toggleStatus'])->middleware(['nivel.acesso:estabelecimentos.editar'])->name('toggle-status');
        
        // Rotas de preços
        Route::get('/{id}/precos', [EstabelecimentoController::class, 'editPrecos'])->middleware(['nivel.acesso:estabelecimentos.editar'])->name('precos');
        Route::put('/{id}/precos', [EstabelecimentoController::class, 'updatePrecos'])->middleware(['nivel.acesso:estabelecimentos.editar'])->name('update-precos');
    });

    // Coletas
    Route::prefix('coletas')->name('coletas.')->middleware(['nivel.acesso:coletas.visualizar'])->group(function () {
        Route::get('/', [ColetaController::class, 'index'])->name('index');
        Route::get('/nova', [ColetaController::class, 'create'])->middleware(['nivel.acesso:coletas.criar'])->name('create');
        Route::post('/nova', [ColetaController::class, 'store'])->middleware(['nivel.acesso:coletas.criar'])->name('store');
        Route::get('/{id}/adicionar-pecas', [ColetaController::class, 'addPecas'])->middleware(['nivel.acesso:coletas.editar'])->name('add-pecas');
        Route::post('/{id}/adicionar-pecas', [ColetaController::class, 'storePecas'])->middleware(['nivel.acesso:coletas.editar'])->name('store-pecas');
        Route::get('/{id}', [ColetaController::class, 'show'])->name('show');
        Route::put('/{id}/cancelar', [ColetaController::class, 'cancelar'])->middleware(['nivel.acesso:coletas.cancelar'])->name('cancelar');
        Route::put('/{id}/concluir', [ColetaController::class, 'concluir'])->middleware(['nivel.acesso:coletas.editar'])->name('concluir');

        // Novas funcionalidades para DESENGOMA e RELAVE
        Route::get('/desengoma/nova', [ColetaController::class, 'createDesengoma'])->middleware(['nivel.acesso:coletas.criar'])->name('create-desengoma');
        Route::post('/desengoma/nova', [ColetaController::class, 'storeDesengoma'])->middleware(['nivel.acesso:coletas.criar'])->name('store-desengoma');
        Route::get('/relave/nova', [ColetaController::class, 'createRelave'])->middleware(['nivel.acesso:coletas.criar'])->name('create-relave');
        Route::post('/relave/nova', [ColetaController::class, 'storeRelave'])->middleware(['nivel.acesso:coletas.criar'])->name('store-relave');
        Route::get('/tipo/{tipo}', [ColetaController::class, 'indexPorTipo'])->name('index-tipo');
        Route::get('/estatisticas/tipo', [ColetaController::class, 'getEstatisticasTipo'])->name('estatisticas-tipo');

        // APIs
        Route::get('/estabelecimento/{estabelecimento_id}/coletas', [ColetaController::class, 'getColetasPorEstabelecimento'])->name('por-estabelecimento');
        Route::get('/{id}/pecas', [ColetaController::class, 'getPecasColeta'])->name('pecas');
        Route::get('/{id}/detalhes', [ColetaController::class, 'getDetalhesColeta'])->name('detalhes');
        Route::get('/tipos/lista', [ColetaController::class, 'getTipos'])->name('tipos');
        Route::get('/dados-atualizados', [ColetaController::class, 'getColetasAtualizadas'])->name('dados-atualizados');
    });
    
    // Pesagem
    Route::prefix('pesagem')->name('pesagem.')->group(function () {
        Route::get('/', [PesagemController::class, 'index'])->name('index');
        Route::get('/cadastrar', [PesagemController::class, 'create'])->name('create');
        Route::post('/', [PesagemController::class, 'store'])->name('store');
        Route::post('/geral', [PesagemController::class, 'storeGeral'])->name('store-geral');
        Route::get('/comparacao', [PesagemController::class, 'createComparacao'])->name('create-comparacao');
        Route::post('/comparacao', [PesagemController::class, 'storeComparacao'])->name('store-comparacao');
        Route::get('/{id}', [PesagemController::class, 'show'])->name('show');
        Route::get('/{id}/editar', [PesagemController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PesagemController::class, 'update'])->name('update');
        Route::delete('/{id}', [PesagemController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/concluir', [PesagemController::class, 'concluir'])->name('concluir');
        Route::patch('/{id}/rascunho', [PesagemController::class, 'definirComoRascunho'])->name('rascunho');

        // APIs
        Route::get('/api/coleta/{coleta_id}', [PesagemController::class, 'getPesagensPorColeta'])->name('api.coleta');
        Route::get('/api/resumo/{coleta_id}', [PesagemController::class, 'getResumoPesagemColeta'])->name('api.resumo');
    });
    
    // Empacotamento
    Route::prefix('empacotamento')->name('empacotamento.')->middleware(['nivel.acesso:empacotamento.visualizar'])->group(function () {
        Route::get('/', [EmpacotamentoController::class, 'index'])->name('index');
        Route::get('/novo', [EmpacotamentoController::class, 'create'])->middleware(['nivel.acesso:empacotamento.criar'])->name('create');
        Route::post('/', [EmpacotamentoController::class, 'store'])->middleware(['nivel.acesso:empacotamento.criar'])->name('store');
        Route::get('/{id}', [EmpacotamentoController::class, 'show'])->name('show');
        Route::get('/{id}/editar', [EmpacotamentoController::class, 'edit'])->middleware(['nivel.acesso:empacotamento.editar'])->name('edit');
        Route::put('/{id}', [EmpacotamentoController::class, 'update'])->middleware(['nivel.acesso:empacotamento.editar'])->name('update');
        Route::put('/{id}/concluir', [EmpacotamentoController::class, 'concluir'])->middleware(['nivel.acesso:empacotamento.editar'])->name('concluir');
        Route::put('/{id}/confirmar-entrega', [EmpacotamentoController::class, 'confirmarEntrega'])->middleware(['nivel.acesso:empacotamento.confirmar_entrega'])->name('confirmar-entrega');
        Route::get('/{id}/reimprimir-qr', [EmpacotamentoController::class, 'reimprimirQR'])->name('reimprimir-qr');
        Route::get('/{id}/etiqueta', [EmpacotamentoController::class, 'gerarEtiqueta'])->name('etiqueta');
        
        // Novas funcionalidades melhoradas
        Route::post('/{id}/finalizar-tipo/{tipoId}', [EmpacotamentoController::class, 'finalizarTipo'])->middleware(['nivel.acesso:empacotamento.editar'])->name('finalizar-tipo');
        Route::post('/{id}/reabrir-tipo/{tipoId}', [EmpacotamentoController::class, 'reabrirTipo'])->middleware(['nivel.acesso:empacotamento.editar'])->name('reabrir-tipo');
        Route::post('/{id}/duplicar-lote', [EmpacotamentoController::class, 'duplicarLote'])->middleware(['nivel.acesso:empacotamento.editar'])->name('duplicar-lote');
        Route::post('/peca/{pecaId}/marcar-relave', [EmpacotamentoController::class, 'marcarRelave'])->middleware(['nivel.acesso:empacotamento.editar'])->name('marcar-relave');
        Route::post('/peca/{pecaId}/marcar-inutilizada', [EmpacotamentoController::class, 'marcarInutilizada'])->middleware(['nivel.acesso:empacotamento.editar'])->name('marcar-inutilizada');
        Route::post('/marcar-impresso', [EmpacotamentoController::class, 'marcarImpresso'])->middleware(['nivel.acesso:empacotamento.editar'])->name('marcar-impresso');
        Route::get('/peca/{pecaId}/reimprimir-etiqueta', [EmpacotamentoController::class, 'reimprimirEtiqueta'])->name('reimprimir-etiqueta');
        Route::get('/funcionarios', [EmpacotamentoController::class, 'getFuncionarios'])->name('funcionarios');
        Route::post('/{id}/imprimir-etiquetas', [EmpacotamentoController::class, 'imprimirEtiquetas'])->name('imprimir-etiquetas');
        Route::get('/{id}/estatisticas', [EmpacotamentoController::class, 'getEstatisticas'])->name('estatisticas');
    });

    // Motorista routes - Acesso restrito apenas para motoristas
    Route::prefix('motorista')->name('motorista.')->withoutMiddleware(['redirecionar.motorista'])->middleware(['nivel.acesso:motorista.visualizar'])->group(function () {
        Route::get('/dashboard', [MotoristaController::class, 'dashboard'])->name('dashboard');
        Route::post('/buscar-empacotamento', [MotoristaController::class, 'buscarEmpacotamento'])->name('buscar-empacotamento');
        Route::post('/confirmar-saida', [MotoristaController::class, 'confirmarSaida'])->name('confirmar-saida');
        Route::post('/confirmar-entrega', [MotoristaController::class, 'confirmarEntrega'])->name('confirmar-entrega');
        
        // Rotas para sacolas individuais
        Route::post('/buscar-sacola', [MotoristaController::class, 'buscarSacola'])->name('buscar-sacola');
        Route::post('/confirmar-saida-sacola', [MotoristaController::class, 'confirmarSaidaSacola'])->name('confirmar-saida-sacola');
        Route::post('/confirmar-todas-sacolas', [MotoristaController::class, 'confirmarTodasSacolas'])->name('confirmar-todas-sacolas');
        
        // Novas funcionalidades melhoradas
        Route::post('/validar-qr-entrega', [MotoristaController::class, 'validarQREntrega'])->name('validar-qr-entrega');
        Route::post('/finalizar-carregamento', [MotoristaController::class, 'finalizarCarregamento'])->name('finalizar-carregamento');
        Route::post('/confirmar-entrega-peca', [MotoristaController::class, 'confirmarEntregaPeca'])->name('confirmar-entrega-peca');
        Route::get('/estatisticas', [MotoristaController::class, 'getEstatisticasMotorista'])->name('estatisticas');
        Route::get('/estabelecimentos', [MotoristaController::class, 'getEstabelecimentos'])->name('estabelecimentos');
    });

    // Relatórios routes
    Route::prefix('relatorios')->name('relatorios.')->group(function () {
        Route::get('/', [App\Http\Controllers\RelatoriosController::class, 'index'])->name('index');
        Route::post('/exportar', [App\Http\Controllers\RelatoriosController::class, 'exportar'])->name('exportar');
    });


    
    // QR Codes
    Route::prefix('qrcodes')->name('qrcodes.')->group(function () {
        Route::get('/{codigo}', [QRCodeController::class, 'rastrear'])->name('rastrear');
        Route::get('/peca/{codigo}', [QRCodeController::class, 'rastrearPeca'])->name('rastrear-peca');
        Route::get('/gerar/{empacotamento_id}', [QRCodeController::class, 'gerar'])->name('gerar');
    });

    // Usuários (apenas para administradores)
    Route::prefix('usuarios')->name('usuarios.')->middleware(['nivel.acesso:usuarios.visualizar'])->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->name('index');
        Route::get('/cadastro', [UsuarioController::class, 'create'])->middleware(['nivel.acesso:usuarios.criar'])->name('create');
        Route::post('/cadastro', [UsuarioController::class, 'store'])->middleware(['nivel.acesso:usuarios.criar'])->name('store');
        Route::get('/{id}', [UsuarioController::class, 'show'])->name('show');
        Route::get('/{id}/editar', [UsuarioController::class, 'edit'])->middleware(['nivel.acesso:usuarios.editar'])->name('edit');
        Route::put('/{id}', [UsuarioController::class, 'update'])->middleware(['nivel.acesso:usuarios.editar'])->name('update');
        Route::delete('/{id}', [UsuarioController::class, 'destroy'])->middleware(['nivel.acesso:usuarios.excluir'])->name('destroy');
        Route::post('/{id}/toggle-status', [UsuarioController::class, 'toggleStatus'])->middleware(['nivel.acesso:usuarios.editar'])->name('toggle-status');
    });

    // Tipos de Peças
    Route::prefix('tipos')->name('tipos.')->middleware(['nivel.acesso:tipos.visualizar'])->group(function () {
        Route::get('/', [TipoController::class, 'index'])->name('index');
        Route::get('/cadastro', [TipoController::class, 'create'])->middleware(['nivel.acesso:tipos.criar'])->name('create');
        Route::post('/cadastro', [TipoController::class, 'store'])->middleware(['nivel.acesso:tipos.criar'])->name('store');
        Route::get('/{id}/editar', [TipoController::class, 'edit'])->middleware(['nivel.acesso:tipos.editar'])->name('edit');
        Route::put('/{id}', [TipoController::class, 'update'])->middleware(['nivel.acesso:tipos.editar'])->name('update');
        Route::delete('/{id}', [TipoController::class, 'destroy'])->middleware(['nivel.acesso:tipos.excluir'])->name('destroy');
        Route::post('/{id}/toggle-status', [TipoController::class, 'toggleStatus'])->middleware(['nivel.acesso:tipos.editar'])->name('toggle-status');
    });

    // Entrega routes - Para motoristas com acesso às funcionalidades de entrega
    Route::prefix('entrega')->name('entrega.')->middleware(['nivel.acesso:motorista.visualizar'])->group(function () {
        Route::get('/dashboard', [EntregaController::class, 'dashboard'])->name('dashboard');
        Route::post('/buscar-empacotamento', [EntregaController::class, 'buscarEmpacotamento'])->name('buscar-empacotamento');
        Route::post('/buscar-peca', [EntregaController::class, 'buscarPeca'])->name('buscar-peca');
        Route::post('/confirmar-saida', [EntregaController::class, 'confirmarSaida'])->name('confirmar-saida');
        Route::post('/confirmar-saida-peca', [EntregaController::class, 'confirmarSaidaPeca'])->name('confirmar-saida-peca');
        Route::post('/confirmar-entrega', [EntregaController::class, 'confirmarEntrega'])->name('confirmar-entrega');
        Route::post('/confirmar-entrega-peca', [EntregaController::class, 'confirmarEntregaPeca'])->name('confirmar-entrega-peca');
        Route::post('/confirmar-entrega-selecionadas', [EntregaController::class, 'confirmarEntregaSelecionadas'])->name('confirmar-entrega-selecionadas');
    });
    
});

// Rotas para API (AJAX)
Route::prefix('api')->middleware(['auth'])->group(function () {
    Route::get('/coletas/{estabelecimento_id}', [ColetaController::class, 'getColetasPorEstabelecimento']);
    Route::get('/coleta/{id}/pecas', [ColetaController::class, 'getPecasColeta']);
    Route::get('/coleta/{id}/detalhes', [ColetaController::class, 'getDetalhesColeta']);

    // Anotações
    Route::prefix('anotacoes')->name('api.anotacoes.')->group(function () {
        Route::get('/', [AnotacaoController::class, 'index'])->name('index');
        Route::post('/', [AnotacaoController::class, 'store'])->name('store');
        Route::put('/{id}', [AnotacaoController::class, 'update'])->name('update');
        Route::delete('/{id}', [AnotacaoController::class, 'destroy'])->name('destroy');
        Route::put('/{id}/resolvida', [AnotacaoController::class, 'marcarResolvida'])->name('marcar-resolvida');
        Route::put('/{id}/nao-resolvida', [AnotacaoController::class, 'marcarNaoResolvida'])->name('marcar-nao-resolvida');
        Route::get('/estatisticas', [AnotacaoController::class, 'estatisticas'])->name('estatisticas');
        Route::get('/relatorio', [AnotacaoController::class, 'relatorio'])->name('relatorio');
    });
});
