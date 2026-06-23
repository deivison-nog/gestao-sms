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
- [XAMPP](https://www.apachefriends.org/) com PHP **≥ 8.0.2**, Apache e MySQL ativos
- [Composer](https://getcomposer.org/) instalado no sistema

> **Nota sobre versões:** o projeto foi configurado para rodar com PHP 8.0+. Se você tiver uma versão mais nova do PHP (8.1, 8.2 ou superior), também funcionará sem problema.

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

> **Composer 2.9+ (aviso de segurança):** se o Composer bloquear a instalação citando advisories do `laravel/framework`, execute o comando abaixo **antes** de `composer install`:
> ```bash
> composer config audit.block-insecure false
> ```
> Este projeto usa Laravel 9 (compatível com PHP 8.0). O Laravel 9 está em fim de vida (EOL), mas é seguro para uso em redes internas/desenvolvimento local.  
> Para ambientes de produção expostos à internet, recomenda-se atualizar o PHP para 8.2+ e usar Laravel 11.

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
| Gestor de unidade | `gestor@gestaosms.local` | `password` |
| Enfermeiro de exemplo | `enfermeiro@gestaosms.local` | `password` |
| Técnico de enfermagem | `tecnico@gestaosms.local` | `password` |

Se precisar recriar apenas os perfis/credenciais de teste:

1. Atualize o autoloader (necessário após `git pull` ou adição de novas classes):
   ```bash
   composer dump-autoload
   ```

2. Execute o seeder:
   - **PowerShell (Windows):**
     ```powershell
     php artisan db:seed --class="Database\Seeders\TestProfilesSeeder"
     ```
   - **Bash / CMD:**
     ```bash
     php artisan db:seed --class=TestProfilesSeeder
     ```

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
