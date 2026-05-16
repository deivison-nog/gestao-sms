# Gestão SMS (Laravel)

Sistema web para gestão de Secretaria Municipal de Saúde com:

- PHP / Laravel
- Bootstrap + JavaScript
- Controle de acesso por função com menu lateral dinâmico
- Módulos: Dashboard, Frequência, Profissionais, Cronograma, Suporte e Enfermagem

## Funcionalidades implementadas

- Autenticação (login/registro)
- Permissões por função/cargo editáveis pelo administrador (incluindo funções/cargos)
- Sidebar dinâmica por permissões de menu
- **Dashboard** com indicadores rápidos
- **Profissionais**: cadastro completo (CPF, nascimento, registro de classe, função, carga horária, regime, lotação), edição, situação ativo/inativo
- **Enfermagem**: listagem de enfermeiros e técnicos de enfermagem
- **Cronograma**: calendário interativo (FullCalendar), criação e edição de escalas
- **Frequência** mensal por profissional (dias trabalhados, faltas e observações)
- **Suporte**: abertura de chamados, respostas e alteração de status (aberto/pendente/fechado)
- **Administração/Funções**: gerenciamento das funções/cargos disponíveis no sistema

---

## Instalação no XAMPP (recomendado)

### Pré-requisitos
- [XAMPP](https://www.apachefriends.org/) com PHP ≥ 8.2, Apache e MySQL ativos
- [Composer](https://getcomposer.org/) instalado no sistema

### Passo a passo

**1. Copie o projeto para a pasta do XAMPP:**
```
C:\xampp\htdocs\gestao-sms\
```

**2. Instale as dependências PHP:**
```bash
cd C:\xampp\htdocs\gestao-sms
composer install
```

**3. Configure o ambiente:**
```bash
copy .env.example .env
php artisan key:generate
```

**4. Crie o banco de dados no MySQL (phpMyAdmin ou linha de comando):**
```sql
CREATE DATABASE gestao_sms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**5. Configure as credenciais no `.env`** (se necessário, ajuste `DB_USERNAME`/`DB_PASSWORD`).

**6. Execute as migrations e popule com dados de demonstração:**
```bash
php artisan migrate --seed
```

**7. Acesse o sistema no navegador:**
```
http://localhost/gestao-sms/public
```

> O arquivo `.htaccess` na raiz do projeto redireciona automaticamente as requisições para a pasta `public/`.  
> Se preferir um VirtualHost dedicado, aponte o `DocumentRoot` diretamente para `gestao-sms/public/` e remova o `.htaccess` raiz.

---

## Usuários padrão (desenvolvimento)

| Usuário | E-mail | Senha |
|---|---|---|
| Administrador | `admin@gestaosms.local` | `password` |
| Enfermeiro de exemplo | `enfermeiro@gestaosms.local` | `password` |

---

## Testes automatizados

```bash
php artisan test
```

---

## Estrutura de campos do cadastro de profissional

| Campo | Obrigatório | Observação |
|---|---|---|
| Nome completo | ✅ | |
| Data de nascimento | — | |
| CPF | ✅ | Formatado automaticamente (máscara JS) |
| Registro de classe | — | Ex: COREN-PE 123456, CRM-PE 54321 |
| Função | — | Selecionada via lista gerenciada pelo administrador |
| Carga horária | — | Ex: 40h, 20h |
| Regime de contratação | — | Efetivo / Temporário / Estágio |
| Situação | ✅ | Ativo / Inativo |
| Lotação | ✅ | Unidade / UBS / ESF |
