# 🔐 Níveis de Acesso - Sistema Lavanderia

## 📋 Estrutura Atualizada

O sistema agora possui **5 níveis de acesso** com permissões específicas para cada função:

---

## 1️⃣ Administrador (ID: 1)
**Descrição:** Acesso completo a todas as funcionalidades do sistema

### Permissões:
- ✅ **Usuários:** criar, editar, excluir, visualizar
- ✅ **Estabelecimentos:** criar, editar, excluir, visualizar
- ✅ **Coletas:** criar, editar, cancelar, visualizar
- ✅ **Pesagem:** criar, editar, visualizar
- ✅ **Empacotamento:** criar, editar, visualizar, confirmar entrega
- ✅ **Motorista:** visualizar
- ✅ **Relatórios:** visualizar, exportar
- ✅ **Tipos:** visualizar, criar, editar, excluir
- ✅ **Status:** visualizar, criar, editar, excluir
- ✅ **QR Codes:** visualizar

---

## 2️⃣ Gestor (ID: 2)
**Descrição:** Gerenciamento de operações e visualização de relatórios

### Permissões:
- ✅ **Estabelecimentos:** visualizar
- ✅ **Coletas:** criar, editar, cancelar, visualizar
- ✅ **Pesagem:** criar, editar, visualizar
- ✅ **Empacotamento:** criar, editar, visualizar, confirmar entrega
- ✅ **Motorista:** visualizar
- ✅ **Relatórios:** visualizar, exportar
- ✅ **Tipos:** visualizar
- ✅ **Status:** visualizar
- ✅ **QR Codes:** visualizar

### Diferenças do Administrador:
- ❌ Não pode gerenciar usuários
- ❌ Não pode gerenciar estabelecimentos
- ❌ Não pode gerenciar tipos e status

---

## 3️⃣ Motorista (ID: 3)
**Descrição:** Acesso específico para confirmação de entregas

### Permissões:
- ✅ **Empacotamento:** visualizar, confirmar entrega
- ✅ **Motorista:** visualizar
- ✅ **QR Codes:** visualizar

### Uso Principal:
- Visualizar pedidos para entrega
- Confirmar entregas realizadas
- Coletar assinaturas dos clientes
- Escanear QR Codes

---

## 4️⃣ Pesagem (ID: 4)
**Descrição:** Acesso para operações de coleta e pesagem

### Permissões:
- ✅ **Estabelecimentos:** visualizar
- ✅ **Coletas:** criar, editar, visualizar
- ✅ **Pesagem:** criar, editar, visualizar
- ✅ **Tipos:** visualizar
- ✅ **Status:** visualizar
- ✅ **QR Codes:** visualizar

### Uso Principal:
- Registrar coletas de roupas
- Realizar pesagem das peças
- Categorizar por tipo
- Atualizar status das coletas

---

## 5️⃣ Empacotamento (ID: 5)
**Descrição:** Acesso para operações de empacotamento

### Permissões:
- ✅ **Estabelecimentos:** visualizar
- ✅ **Coletas:** visualizar
- ✅ **Pesagem:** visualizar
- ✅ **Empacotamento:** criar, editar, visualizar
- ✅ **Tipos:** visualizar
- ✅ **Status:** visualizar
- ✅ **QR Codes:** visualizar

### Uso Principal:
- Empacotar peças lavadas
- Registrar conclusão de empacotamento
- Preparar pedidos para entrega
- Visualizar informações de pesagem

---

## 🔄 Mudanças Realizadas

### Níveis Removidos:
- ❌ **Operador** (ID: 2) → Transformado em **Gestor**
- ❌ **Visualizador** (ID: 4) → Removido (usuários migrados para Gestor)

### Níveis Adicionados:
- ✅ **Pesagem** (ID: 4) - Novo
- ✅ **Empacotamento** (ID: 5) - Novo

---

## 📁 Arquivos Modificados

1. **database/seeders/NiveisAcessoSeeder.php** - Atualizado com 5 níveis
2. **database/seeders/UsuariosAdicionaisSeeder.php** - Usuários de exemplo atualizados
3. **database/seeders/EmpacotamentoSeeder.php** - Busca por Gestor/Empacotamento
4. **database/seeders/ColetaSeeder.php** - Busca por Gestor/Pesagem
5. **lavanderia.sql** - Dump do banco atualizado
6. **database/migrations/2025_10_04_000001_update_niveis_acesso_structure.php** - Migration criada

---

## 🚀 Como Aplicar as Mudanças

### Opção 1: Migration (Banco Existente)
```bash
php artisan migrate
```
Isso irá:
- Atualizar permissões do Administrador
- Transformar Operador em Gestor
- Migrar usuários do Visualizador para Gestor
- Criar níveis Pesagem e Empacotamento

### Opção 2: Fresh Install (Banco Novo)
```bash
php artisan migrate:fresh --seed
```
Isso irá recriar todo o banco com os novos níveis.

### Opção 3: SQL Direto
Importe o arquivo `lavanderia.sql` atualizado no seu banco de dados.

---

## 👥 Usuários de Teste Atualizados

### Gestores:
- ana.gestor@lavanderia.com (senha: 123456)
- roberto.gestor@lavanderia.com (senha: 123456)

### Pesagem:
- pedro.pesagem@lavanderia.com (senha: 123456)

### Empacotamento:
- mariana.empacotamento@lavanderia.com (senha: 123456)

### Motoristas:
- lucas.motorista@lavanderia.com (senha: 123456)
- rafael.motorista@lavanderia.com (senha: 123456)

---

## 🔍 Como Verificar Permissões

No código, você pode verificar permissões de um usuário:

```php
// Verificar se usuário tem permissão específica
if (auth()->user()->temPermissao('coletas.criar')) {
    // Usuário pode criar coletas
}

// No middleware (routes/web.php)
Route::get('/coletas', [ColetaController::class, 'index'])
    ->middleware(['auth', 'nivel_acesso:coletas.visualizar']);

// No Blade
@if(auth()->user()->temPermissao('coletas.criar'))
    <button>Nova Coleta</button>
@endif
```

---

## ⚠️ Observações Importantes

1. **Usuários Existentes:** Usuários com nível "Visualizador" foram automaticamente migrados para "Gestor"
2. **Backup:** Faça backup do banco antes de aplicar a migration
3. **Testes:** Teste todas as funcionalidades após a migração
4. **Controllers:** Revise os controllers para garantir que as verificações de permissão estão corretas

---

## 📞 Suporte

Em caso de dúvidas sobre as permissões ou implementação, consulte:
- `app/Http/Middleware/VerificarNivelAcesso.php` - Middleware de verificação
- `app/Models/Usuario.php` - Método `temPermissao()`
- `app/Models/NivelAcesso.php` - Modelo de níveis de acesso

