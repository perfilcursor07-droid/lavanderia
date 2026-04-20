<!-- Caixa de Anotações Flutuante -->
<div id="notes-box" class="fixed right-4 top-1/2 transform -translate-y-1/2 z-50 transition-all duration-300 ease-in-out">
    <!-- Botão para Minimizar/Maximizar -->
    <div id="notes-toggle" class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-l-lg cursor-pointer shadow-lg transition-colors duration-200">
        <svg id="notes-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
    </div>

    <!-- Conteúdo da Caixa -->
    <div id="notes-content" class="bg-white border border-gray-200 rounded-r-lg rounded-bl-lg shadow-xl w-80 max-h-96 hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4 rounded-tr-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-sm">Anotações de Melhorias</h3>
                    <p id="current-module" class="text-xs opacity-90">Módulo Atual</p>
                </div>
                <button id="notes-close" class="text-white hover:text-gray-200 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Conteúdo -->
        <div class="p-4">
            <!-- Categorias -->
            <div class="mb-3">
                <div class="flex space-x-1 text-xs mb-2">
                    <button class="category-btn active px-2 py-1 bg-green-100 text-green-700 rounded" data-category="melhorias">
                        ✨ Melhorias
                    </button>
                    <button class="category-btn px-2 py-1 bg-yellow-100 text-yellow-700 rounded" data-category="alteracoes">
                        🔧 Alterações
                    </button>
                    <button class="category-btn px-2 py-1 bg-red-100 text-red-700 rounded" data-category="exclusoes">
                        🗑️ Exclusões
                    </button>
                </div>

                <!-- Filtro de Status -->
                <div class="flex items-center justify-center">
                    <label class="flex items-center text-xs text-gray-600">
                        <input type="checkbox" id="show-resolved" class="mr-1 text-xs">
                        Mostrar resolvidas
                    </label>
                </div>
            </div>

            <!-- Área de Texto -->
            <div class="mb-3">
                <textarea id="notes-textarea" 
                          placeholder="Digite suas anotações aqui..."
                          class="w-full h-32 p-2 text-sm border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <!-- Botões -->
            <div class="flex justify-between items-center">
                <div class="text-xs text-gray-500">
                    <span id="char-count">0</span>/500 caracteres
                </div>
                <div class="flex space-x-2">
                    <button id="notes-clear" class="px-3 py-1 text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 rounded transition-colors duration-200">
                        Limpar
                    </button>
                    <button id="notes-save" class="px-3 py-1 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors duration-200">
                        Salvar
                    </button>
                </div>
            </div>

            <!-- Lista de Anotações Salvas -->
            <div class="mt-4 border-t pt-3">
                <h4 class="text-xs font-medium text-gray-700 mb-2">Anotações Salvas:</h4>
                <div id="saved-notes" class="space-y-2 max-h-32 overflow-y-auto">
                    <!-- Anotações serão inseridas aqui via JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos específicos para a caixa de anotações */
#notes-box {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.category-btn.active {
    font-weight: 600;
    transform: scale(1.05);
}

.category-btn {
    transition: all 0.2s ease;
}

.category-btn:hover {
    transform: scale(1.02);
}

.note-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 8px;
    position: relative;
}

.note-item .note-category {
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.note-item .note-text {
    font-size: 11px;
    line-height: 1.4;
    margin-top: 4px;
}

.note-item .note-page {
    font-size: 10px;
    margin-top: 4px;
}

.note-page-link {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

.note-page-link:hover {
    color: #1d4ed8;
    text-decoration: underline;
}

.note-item .note-date {
    font-size: 10px;
    color: #64748b;
    margin-top: 4px;
}

.note-item .note-actions {
    position: absolute;
    top: 4px;
    right: 4px;
    display: flex;
    gap: 2px;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.note-item:hover .note-actions {
    opacity: 1;
}

.note-action-btn {
    width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    cursor: pointer;
    font-size: 10px;
    font-weight: bold;
    border: none;
    transition: all 0.2s ease;
}

.note-edit-btn {
    background: #3b82f6;
    color: white;
}

.note-edit-btn:hover {
    background: #2563eb;
    transform: scale(1.1);
}

.note-resolve-btn {
    background: #10b981;
    color: white;
}

.note-resolve-btn:hover {
    background: #059669;
    transform: scale(1.1);
}

.note-unresolve-btn {
    background: #f59e0b;
    color: white;
}

.note-unresolve-btn:hover {
    background: #d97706;
    transform: scale(1.1);
}

.note-delete-btn {
    background: #ef4444;
    color: white;
}

.note-delete-btn:hover {
    background: #dc2626;
    transform: scale(1.1);
}

.note-resolved {
    background: #f0fdf4;
    border-color: #bbf7d0;
    opacity: 0.8;
}

.note-resolved .note-text {
    text-decoration: line-through;
    color: #6b7280;
}

.note-resolved-badge {
    background: #10b981;
    color: white;
    font-size: 9px;
    padding: 2px 6px;
    border-radius: 10px;
    margin-left: 8px;
    font-weight: 600;
}

/* Animações */
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.slide-in {
    animation: slideIn 0.3s ease-out;
}

.slide-out {
    animation: slideOut 0.3s ease-in;
}

/* Responsividade */
@media (max-width: 768px) {
    #notes-box {
        right: 8px;
        top: auto;
        bottom: 20px;
        transform: none;
    }
    
    #notes-content {
        width: calc(100vw - 32px);
        max-width: 320px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // URL base da API
    const API_BASE_URL = '{{ url("/api/anotacoes") }}';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const notesBox = document.getElementById('notes-box');
    const notesToggle = document.getElementById('notes-toggle');
    const notesContent = document.getElementById('notes-content');
    const notesClose = document.getElementById('notes-close');
    const notesTextarea = document.getElementById('notes-textarea');
    const notesSave = document.getElementById('notes-save');
    const notesClear = document.getElementById('notes-clear');
    const savedNotesContainer = document.getElementById('saved-notes');
    const currentModuleSpan = document.getElementById('current-module');
    const charCount = document.getElementById('char-count');
    const categoryButtons = document.querySelectorAll('.category-btn');
    const showResolvedCheckbox = document.getElementById('show-resolved');

    let isOpen = false;
    let currentCategory = 'melhorias';
    let currentModule = getCurrentModule();
    let showResolved = false;
    
    // Inicializar
    init();
    
    function init() {
        updateCurrentModule();
        loadSavedNotes();
        setupEventListeners();
    }
    
    function getCurrentModule() {
        const path = window.location.pathname;
        if (path.includes('/estabelecimentos')) return 'estabelecimentos';
        if (path.includes('/coletas')) return 'coletas';
        if (path.includes('/empacotamento')) return 'empacotamento';
        if (path.includes('/painel')) return 'painel';
        return 'geral';
    }

    function getCurrentPageName() {
        const path = window.location.pathname;
        const url = window.location.href;

        // Mapeamento de URLs para nomes amigáveis
        const pageNames = {
            // Estabelecimentos
            '/estabelecimentos': 'Listagem de Estabelecimentos',
            '/estabelecimentos/cadastro': 'Cadastro de Estabelecimento',
            '/estabelecimentos/': 'Detalhes do Estabelecimento',
            '/estabelecimentos/editar': 'Edição de Estabelecimento',

            // Coletas
            '/coletas': 'Listagem de Coletas',
            '/coletas/nova': 'Nova Coleta',
            '/coletas/': 'Detalhes da Coleta',

            // Empacotamento
            '/empacotamento': 'Listagem de Empacotamentos',
            '/empacotamento/novo': 'Novo Empacotamento',
            '/empacotamento/': 'Detalhes do Empacotamento',

            // Painel
            '/painel': 'Dashboard Principal',

            // Geral
            '/': 'Página Inicial'
        };

        // Verificar correspondência exata primeiro
        if (pageNames[path]) {
            return pageNames[path];
        }

        // Verificar padrões com IDs
        for (const [pattern, name] of Object.entries(pageNames)) {
            if (pattern.endsWith('/') && path.includes(pattern) && path !== pattern.slice(0, -1)) {
                // É uma página de detalhes/edição com ID
                const segments = path.split('/');
                const id = segments[segments.length - 1];
                const action = segments[segments.length - 2];

                if (action === 'editar') {
                    return name.replace('Detalhes', 'Edição');
                } else if (!isNaN(id)) {
                    return name.replace('Detalhes', `Detalhes (#${id})`);
                }
            }
        }

        // Fallback para nome baseado no path
        const segments = path.split('/').filter(s => s);
        if (segments.length > 0) {
            const lastSegment = segments[segments.length - 1];
            const module = segments[0];

            if (lastSegment === 'cadastro' || lastSegment === 'nova' || lastSegment === 'novo') {
                return `Cadastro - ${module.charAt(0).toUpperCase() + module.slice(1)}`;
            } else if (lastSegment === 'editar') {
                return `Edição - ${module.charAt(0).toUpperCase() + module.slice(1)}`;
            } else if (!isNaN(lastSegment)) {
                return `Detalhes (#${lastSegment}) - ${module.charAt(0).toUpperCase() + module.slice(1)}`;
            }
        }

        return 'Página Atual';
    }
    
    function updateCurrentModule() {
        const moduleNames = {
            'estabelecimentos': 'Estabelecimentos',
            'coletas': 'Coletas',
            'empacotamento': 'Empacotamento',
            'painel': 'Painel',
            'geral': 'Geral'
        };
        currentModuleSpan.textContent = moduleNames[currentModule] || 'Módulo Atual';
    }
    
    function setupEventListeners() {
        // Toggle da caixa
        notesToggle.addEventListener('click', toggleNotesBox);
        notesClose.addEventListener('click', closeNotesBox);
        
        // Botões de categoria
        categoryButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                setActiveCategory(this.dataset.category);
            });
        });
        
        // Textarea
        notesTextarea.addEventListener('input', updateCharCount);
        notesTextarea.addEventListener('input', function() {
            if (this.value.length > 500) {
                this.value = this.value.substring(0, 500);
            }
            updateCharCount();
        });
        
        // Botões de ação
        notesSave.addEventListener('click', saveNote);
        notesClear.addEventListener('click', clearTextarea);

        // Checkbox de mostrar resolvidas
        showResolvedCheckbox.addEventListener('change', function() {
            showResolved = this.checked;
            loadSavedNotes();
        });

        // Fechar ao clicar fora
        document.addEventListener('click', function(e) {
            if (isOpen && !notesBox.contains(e.target)) {
                closeNotesBox();
            }
        });
    }
    
    function toggleNotesBox() {
        if (isOpen) {
            closeNotesBox();
        } else {
            openNotesBox();
        }
    }
    
    function openNotesBox() {
        notesContent.classList.remove('hidden');
        notesContent.classList.add('slide-in');
        isOpen = true;
        notesTextarea.focus();
    }
    
    function closeNotesBox() {
        notesContent.classList.add('slide-out');
        setTimeout(() => {
            notesContent.classList.add('hidden');
            notesContent.classList.remove('slide-in', 'slide-out');
        }, 300);
        isOpen = false;
    }
    
    function setActiveCategory(category) {
        currentCategory = category;
        categoryButtons.forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.category === category) {
                btn.classList.add('active');
            }
        });
    }
    
    function updateCharCount() {
        charCount.textContent = notesTextarea.value.length;
    }
    
    function saveNote() {
        const text = notesTextarea.value.trim();
        if (!text) {
            alert('Digite uma anotação antes de salvar!');
            return;
        }

        // Mostrar loading
        notesSave.textContent = 'Salvando...';
        notesSave.disabled = true;

        // Dados para enviar
        const data = {
            modulo: currentModule,
            pagina: window.location.pathname,
            pagina_nome: getCurrentPageName(),
            categoria: currentCategory,
            texto: text
        };

        // Enviar para o servidor
        fetch(API_BASE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // Limpar textarea
                clearTextarea();

                // Recarregar lista
                loadSavedNotes();

                // Feedback visual de sucesso
                notesSave.textContent = 'Salvo!';
                notesSave.classList.add('bg-green-600');
                setTimeout(() => {
                    notesSave.textContent = 'Salvar';
                    notesSave.classList.remove('bg-green-600');
                    notesSave.disabled = false;
                }, 1500);
            } else {
                throw new Error(result.message || 'Erro ao salvar anotação');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao salvar anotação: ' + error.message);
            notesSave.textContent = 'Salvar';
            notesSave.disabled = false;
        });
    }
    
    function clearTextarea() {
        notesTextarea.value = '';
        updateCharCount();
    }
    
    function loadSavedNotes() {
        // Buscar anotações do servidor
        const url = `${API_BASE_URL}?modulo=${currentModule}${showResolved ? '' : '&resolvida=false'}`;
        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                savedNotesContainer.innerHTML = '';

                if (result.anotacoes.length === 0) {
                    savedNotesContainer.innerHTML = '<p class="text-xs text-gray-500 italic">Nenhuma anotação ainda</p>';
                    return;
                }

                result.anotacoes.forEach(note => {
                    const noteElement = createNoteElement(note);
                    savedNotesContainer.appendChild(noteElement);
                });
            } else {
                console.error('Erro ao carregar anotações:', result.message);
                savedNotesContainer.innerHTML = '<p class="text-xs text-red-500 italic">Erro ao carregar anotações</p>';
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            savedNotesContainer.innerHTML = '<p class="text-xs text-red-500 italic">Erro ao carregar anotações</p>';
        });
    }
    
    function createNoteElement(note) {
        const div = document.createElement('div');
        div.className = `note-item ${note.resolvida ? 'note-resolved' : ''}`;

        const categoryColors = {
            'melhorias': 'text-green-600',
            'alteracoes': 'text-yellow-600',
            'exclusoes': 'text-red-600'
        };

        const resolvedBadge = note.resolvida ?
            '<span class="note-resolved-badge">✓ Resolvida</span>' : '';

        const actionButtons = note.resolvida ?
            `<div class="note-actions">
                <button class="note-action-btn note-unresolve-btn" onclick="markAsUnresolved(${note.id})" title="Marcar como não resolvida">
                    ↶
                </button>
                <button class="note-action-btn note-delete-btn" onclick="deleteNote(${note.id})" title="Excluir">
                    ×
                </button>
            </div>` :
            `<div class="note-actions">
                <button class="note-action-btn note-edit-btn" onclick="editNote(${note.id}, '${note.texto.replace(/'/g, "\\'")}', '${note.categoria}')" title="Editar">
                    ✏️
                </button>
                <button class="note-action-btn note-resolve-btn" onclick="markAsResolved(${note.id})" title="Marcar como resolvida">
                    ✓
                </button>
                <button class="note-action-btn note-delete-btn" onclick="deleteNote(${note.id})" title="Excluir">
                    ×
                </button>
            </div>`;

        div.innerHTML = `
            <div class="note-category ${categoryColors[note.categoria]}">
                ${note.categoria_icone} ${note.categoria_formatada.toUpperCase()}
                ${resolvedBadge}
            </div>
            <div class="note-text" id="note-text-${note.id}">${note.texto}</div>
            <div class="note-page">
                <a href="${note.pagina}" class="note-page-link" title="Ir para a página">
                    🔗 ${note.pagina_nome || note.pagina}
                </a>
            </div>
            <div class="note-date">${note.data_formatada}</div>
            ${actionButtons}
        `;

        return div;
    }
    
    // Função global para deletar nota
    window.deleteNote = function(noteId) {
        if (confirm('Deseja excluir esta anotação?')) {
            fetch(`${API_BASE_URL}/${noteId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    loadSavedNotes();
                } else {
                    alert('Erro ao excluir anotação: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao excluir anotação');
            });
        }
    };

    // Função global para editar nota
    window.editNote = function(noteId, currentText, currentCategory) {
        const newText = prompt('Editar anotação:', currentText);
        if (newText !== null && newText.trim() !== '' && newText !== currentText) {
            fetch(`${API_BASE_URL}/${noteId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({
                    texto: newText.trim()
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    loadSavedNotes();
                } else {
                    alert('Erro ao editar anotação: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao editar anotação');
            });
        }
    };

    // Função global para marcar como resolvida
    window.markAsResolved = function(noteId) {
        const observacao = prompt('Observação sobre a resolução (opcional):');
        if (observacao !== null) {
            fetch(`${API_BASE_URL}/${noteId}/resolvida`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({
                    observacao: observacao.trim()
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    loadSavedNotes();
                } else {
                    alert('Erro ao marcar como resolvida: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao marcar como resolvida');
            });
        }
    };

    // Função global para marcar como não resolvida
    window.markAsUnresolved = function(noteId) {
        if (confirm('Deseja marcar esta anotação como não resolvida?')) {
            fetch(`${API_BASE_URL}/${noteId}/nao-resolvida`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    loadSavedNotes();
                } else {
                    alert('Erro ao marcar como não resolvida: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao marcar como não resolvida');
            });
        }
    };
});
</script>
