# 📚 Documentação - OWL School

Sistema de gestão escolar com suporte a alunos, professores, responsáveis e admin.

## 🗂️ Índice de Documentação

### 📖 Documentos Principais

- **[📋 ARCHITECTURE.md](ARCHITECTURE.md)** - Arquitetura geral da aplicação
- **[🔧 DEVELOPMENT.md](DEVELOPMENT.md)** - Guia para desenvolvedores
- **[📝 LOGGER.md](LOGGER.md)** - Sistema de logs
- **[🔌 API_REFERENCE.md](API_REFERENCE.md)** - Referência de todos os endpoints

### 🎯 Todos os Módulos

A API inclui 9 módulos completos (AUTH, TAREFA, CHAMADA, CHAMADA_ITEM, COMUNICADO, ADVERTENCIA, AGENDA, PROVA, PROVA_NOTA).

Ver todos os endpoints em **[API_REFERENCE.md](API_REFERENCE.md)**

---

## 🚀 Quick Start

### Para Começar a Desenvolver

1. Leia [ARCHITECTURE.md](ARCHITECTURE.md) para entender a estrutura
2. Abra [DEVELOPMENT.md](DEVELOPMENT.md) para padrões e exemplos
3. Consulte o módulo relevante em `modules/`

### Para Integrar uma API

1. Abra [API_REFERENCE.md](API_REFERENCE.md) para listar os endpoints
2. Consulte o módulo específico para detalhes de payload

### Para Debugar

1. Leia [LOGGER.md](LOGGER.md) para entender logs
2. Verifique `logs/app.log` na raiz do projeto

---

## 📊 Tecnologias

- **Backend**: PHP 8+ com MySQLi
- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Database**: MySQL 5.7+
- **Padrão Arquitetural**: Repository + Service + DTO

---

## 🔑 Tipos de Usuário

- **aluno** - Acesso a tarefas, provas, agenda
- **professor** - Cria tarefas, provas, chamadas
- **responsavel** - Visualiza desempenho do aluno
- **admin** - Acesso total ao sistema

---

## 📝 Estilo de Código

- Todos os DTOs aceitam ambos `snake_case` e `camelCase`
- Services sempre retornam `['success' => bool, 'message' => string, 'status' => int]`
- Controllers estendem `BaseController` para error handling centralizado
- Repositories sempre checam `if (!$stmt)` antes de usar

---

## 👥 Suporte

Para dúvidas sobre:
- **Arquitetura** → [ARCHITECTURE.md](ARCHITECTURE.md)
- **Desenvolvimento** → [DEVELOPMENT.md](DEVELOPMENT.md)
- **Endpoints HTTP** → [API_REFERENCE.md](API_REFERENCE.md)
- **Logs e Debug** → [LOGGER.md](LOGGER.md)

---

Última atualização: Março 2026
