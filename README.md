# Gestão SMS (Laravel)

Base inicial de um sistema web para gestão de Secretaria Municipal de Saúde com:

- PHP / Laravel
- Bootstrap + JavaScript
- Controle de acesso por função com menu lateral dinâmico
- Módulos: Dashboard, Frequência, Profissionais, Cronograma, Suporte e Enfermagem

## Funcionalidades MVP implementadas

- Autenticação (login/registro)
- Permissões por função/cargo editáveis por administrador
- Sidebar dinâmica por permissões de menu
- **Dashboard** com indicadores rápidos
- **Profissionais**: cadastro, listagem, edição, ativação/inativação e vínculo opcional com usuário
- **Enfermagem**: listagem de enfermeiros e técnicos de enfermagem
- **Cronograma**: calendário interativo (FullCalendar), criação e edição de escalas
- **Frequência** mensal por profissional (dias trabalhados, faltas e observações)
- **Suporte**: abertura de chamados, respostas e alteração de status (aberto/pendente/fechado)

## Instalação

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure o banco de dados no `.env` e rode:

```bash
php artisan migrate --seed
php artisan serve
```

Acesse: `http://127.0.0.1:8000`

## Usuários padrão (desenvolvimento)

- **Administrador**
  - E-mail: `admin@gestaosms.local`
  - Senha: `password`
- **Enfermeiro de exemplo**
  - E-mail: `enfermeiro@gestaosms.local`
  - Senha: `password`

## Testes

```bash
php artisan test
```

