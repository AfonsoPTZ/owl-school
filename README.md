# 🦉 OWL School

Plataforma escolar profissional com arquitetura em camadas, desenvolvida para facilitar a comunicação entre **alunos, responsáveis, professores e administradores**.

Projeto acadêmico com padrões corporativos: **PHP 8 + MySQL + Bootstrap 5** com **Controller → Service → Repository** pattern.

---

## ✨ Destaques Arquiteturais

**Arquitetura em Camadas**
- Pattern: Controller → Service → Repository
- Cada componente com responsabilidade única
- Fácil de testar, manter e expandir

**DTOs Inteligentes**
- Normaliza JSON (camelCase) e FormData (snake_case)
- Conversão automática de tipos
- Contrato bem definido entre camadas

**Validação Robusta**
- Validator em cada módulo
- validateCreate(), validateUpdate(), validateDelete()
- Regras de negócio centralizadas

**Segurança**
- Prepared Statements em todas as queries (sem SQL Injection)
- Input Validation obrigatória
- Authentication Middleware
- Tratamento de erros centralizado

**Logging Centralizado**
- Sistema de logs em `src/utils/Logger.php`
- Rastreamento de operações e erros
- Arquivo em `/logs/app.log`

**9 Módulos CRUD Completos**
- AUTH, TAREFA, CHAMADA, CHAMADA_ITEM, COMUNICADO, ADVERTENCIA, AGENDA, PROVA, PROVA_NOTA

---

## 📚 Funcionalidades

- Login com perfis diferentes (Aluno, Professor, Responsável, Admin)
- Dashboard personalizado para cada usuário
- Visualização de notas, faltas e boletim
- Cadastro e vínculo de alunos e responsáveis
- Comunicação entre escola e responsáveis (avisos, mensagens)
- Gestão de disciplinas, turmas e professores
- Controle de presença (chamadas)
- Painel administrativo para gerenciamento

---

## 🛠️ Stack Tecnológico

| Componente | Tecnologia |
|-----------|-----------|
| **Frontend** | HTML5, CSS3, [Bootstrap 5](https://getbootstrap.com/) |
| **Backend** | PHP 8+ |
| **Banco de Dados** | MySQL 5.7+ |
| **Autenticação** | Session + Cookie |
| **Padrão Arquitetural** | Repository + Service + DTO |
| **Configuração** | .env para variáveis sensíveis |

---

## 📂 Estrutura

```
OWL School/
├── src/
│   ├── api/           # Endpoints HTTP (entrada das requisições)
│   ├── controllers/   # Coordena requisições (Controller pattern)
│   ├── services/      # Lógica de negócio
│   ├── repositories/  # Acesso ao banco de dados
│   ├── dtos/          # Data Transfer Objects (normalizam dados)
│   ├── validators/    # Validação de regras de negócio
│   ├── models/        # Modelos de dados
│   ├── middleware/    # Autenticação e autorização
│   ├── db/            # Configuração e scripts SQL
│   └── utils/         # Utilitários (Logger, etc)
│
├── public/            # Frontend estático
│   ├── aluno/         # Painel do aluno
│   ├── professor/     # Painel do professor
│   ├── responsavel/   # Painel do responsável
│   ├── admin/         # Painel administrativo
│   ├── assets/        # CSS, JavaScript, imagens
│   └── index.html     # Página de login
│
├── docs/              # Documentação
│   ├── HIGHLIGHTS.md  # Destaques do projeto
│   ├── ARCHITECTURE.md  # Como funciona o código
│   ├── DEVELOPMENT.md  # Como criar novo módulo
│   └── LOGGER.md      # Sistema de logs
│
├── logs/              # Arquivos de log da aplicação
├── vendor/            # Dependências Composer
├── .env               # Variáveis de configuração (não commitar)
├── composer.json      # Dependências PHP
└── README.md          # Este arquivo
```

---

## 📖 Documentação

Toda documentação técnica está em `/docs/`:

- **[HIGHLIGHTS.md](docs/HIGHLIGHTS.md)** - Destaques do projeto ⭐
- **[ARCHITECTURE.md](docs/ARCHITECTURE.md)** - Arquitetura e fluxo
- **[ENDPOINTS.md](docs/ENDPOINTS.md)** - Contrato dos endpoints 📋
- **[DEVELOPMENT.md](docs/DEVELOPMENT.md)** - Como criar novo módulo
- **[LOGGER.md](docs/LOGGER.md)** - Sistema de logging

---

## 🚀 Como Rodar

### Pré-requisitos
- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL)
- PHP 8+
- Composer (opcional, já tem dependências em `vendor/`)

### Instalação

1. Clone o repositório em `htdocs/`:
   ```bash
   git clone https://github.com/AfonsoPTZ/owl-school.git
   cd owl-school
   ```

2. Configure o banco de dados:
   - Abra o phpMyAdmin (http://localhost/phpmyadmin)
   - Crie um banco chamado `owl_school`
   - Importe o schema:
     ```bash
     mysql -u root owl_school < src/db/schema.sql
     mysql -u root owl_school < src/db/seed.sql
     ```

3. Configure variáveis de ambiente:
   ```bash
   cp .env.example .env
   # Edite .env com suas credenciais do banco
   ```

4. Inicie XAMPP:
   - Apache ON
   - MySQL ON

5. Acesse no navegador:
   ```
   http://localhost/owl-school/public/
   ```

### Usuários de Teste

Use as credenciais no `src/db/seed.sql`:

| Email | Senha | Tipo |
|-------|-------|------|
| joao.aluno@teste.com | 123456 | Aluno |
| maria.prof@teste.com | 123456 | Professor |
| carlos.resp@teste.com | 123456 | Responsável |
| admin@teste.com | 123456 | Admin |

---

## 🧪 Testes

Flow tests que validam o fluxo completo da aplicação:

```bash
php tests/tarefa_flow_test.php
```

Cada teste passa por todas as camadas:
**Controller → Service → Repository → Database**

Veja [tests/README.md](tests/README.md) para mais informações.

---

- ✅ Prepared Statements em tudo (sem SQL Injection)
- ✅ Validação rigorosa de input
- ✅ Session-based authentication
- ✅ Authorization middleware
- ✅ Logging de operações críticas
- ✅ .env para dados sensíveis

---

## 📝 Autor

Desenvolvido como projeto acadêmico em 2026.

---
