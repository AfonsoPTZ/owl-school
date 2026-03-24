# 🤖 IA - Assistente Inteligente

Documentação completa do assistente de IA com Google Gemini.

---

## 📌 Visão Geral

O assistente IA da OWL School é um **híbrido inteligente**:

1. **Primeira tentativa:** Usa Google Gemini para classificação e geração inteligente
2. **Fallback:** Se Gemini falhar/indisponível, usa keywords pré-configuradas
3. **Contexto:** Mantém histórico de conversa para follow-ups

---

## 🏗️ Arquitetura em Camadas

```
Frontend (JavaScript)
    ↓
POST /api/ia/chat
    ↓
AIController::index()
    ↓
AIService::chat()
    ├─ IntentDetector→detect()  (Classifica pergunta)
    ├─ Fetch dados necessários    (Repository)
    ├─ FollowUpDetector           (Contexto anterior)
    ├─ PromptBuilder→build()      (Monta prompt)
    ├─ GeminiClient→generate()    (Comunica com Gemini)
    └─ AnswerFormatter→format()   (Formata resposta)
    ↓
Response JSON com resposta natural
```

---

## 🎯 Pipeline em Detalhe

### 1️⃣ IntentDetector - Classifica a Pergunta

Detecta qual é a intenção por trás da pergunta.

**Fluxo:**
```
Pergunta: "Quais são minhas tarefas?"
        ↓
[Tenta Gemini]
├─ Se Gemini disponível:
│  └─ Envia pra classificação
│     └─ Recebe: {intent: "consultar_tarefas"}
│
└─ Se Gemini indisponível:
   └─ Busca keywords em português
      └─ Encontra "tarefa" no mapa
         └─ Retorna: {intent: "consultar_tarefas"}
```

**Intenções reconhecidas:**
```
consultar_tarefas       → "tarefa", "dever", "atividade", "lição"
consultar_provas        → "prova", "teste", "avaliação", "exame"
consultar_notas         → "nota", "boletim", "resultado", "média"
consultar_advertencias  → "advertência", "ocorrência", "repreensão"
consultar_agenda        → "agenda", "aula", "horário", "segunda", "quinta"
consultar_chamada       → "chamada", "frequência", "presença", "falta"
consultar_comunicados   → "comunicado", "recado", "aviso geral"
desconhecido            → Fora do escopo
```

### 2️⃣ AIService - Orquestra Tudo

Centro de decisão que coordena a busca de dados e geração de resposta.

```php
public function chat(AIDTO $dto): array {
    // 1. Valida pergunta
    $validacao = $this->validator->validateQuestion($dto);
    
    // 2. Recupera contexto anterior (para follow-ups)
    $conversationContext = $this->contextManager->getConversationContext();
    
    // 3. Detecta intenção
    $intentResult = $this->intentDetector->detect($dto->pergunta);
    // {intent: 'consultar_tarefas', ...}
    
    // 4. Se for follow-up, reutiliza dados
    if ($this->followUpDetector->isFollowUp(...)) {
        $dados = $conversationContext['last_data'];
    } else {
        // 5. Busca dados da intenção
        $dados = $this->fetchDataByIntent($intentResult, $userContext);
    }
    
    // 6. Gera resposta (Gemini ou fallback)
    $answer = $this->generateAnswer(...);
    
    // 7. Salva contexto para próxima pergunta
    $this->contextManager->saveConversationContext($intentResult['intent'], $dados, $answer);
    
    return $answer;
}
```

### 3️⃣ Busca de Dados - Repositories

Busca dados relevantes conforme a intenção:

```php
'consultar_tarefas' => $tarefaRepository->findAll()
'consultar_provas' => $provaRepository->findAll()
'consultar_notas' => {
    if (aluno) → $utilsAlunoRepository->getNotas($userId)
    if (responsavel) → $utilsResponsavelRepository->getNotasFilho($userId)
}
'consultar_agenda' => $agendaRepository->findAll()
'consultar_chamada' => {
    if (aluno) → $utilsAlunoRepository->getFrequencias($userId)
    if (responsavel) → $utilsResponsavelRepository->getFrequenciasFilho($userId)
}
```

### 4️⃣ FollowUpDetector - Contexto

Detecta se a pergunta é um follow-up (para reutilizar dados):

**Heurísticas:**
```
if (pergunta contém: "qual", "quais", "quando", "onde", "como") 
    → É follow-up (sabe qual era o assunto anterior)

if (pergunta muito curta: < 15 caracteres)
    → Provavelmente follow-up ("?", "E aí?", "Qual?")

if (há contexto anterior && contexto anterior foi mesma intenção)
    → Reutiliza dados já buscados (mais rápido!)
```

**Exemplo:**
```
1a pergunta: "Quais são minhas tarefas?"
    → Busca tasks, retorna 3 tarefas
    → ContextManager salva resultado

2a pergunta: "Qual é o prazo?"
    → FollowUpDetector detém "qual" → é follow-up
    → Reutiliza as 3 tarefas já buscadas
    → Mais rápido, sem nova query
```

### 5️⃣ PromptBuilder - Monta Prompt

Constrói payload otimizado para o Gemini.

**System Instruction:**
```
"Você é o assistente escolar do Owl School.
- Responda em português do Brasil
- Seja claro, curto e útil
- Use APENAS os dados fornecidos
- Não invente informações
- O usuário logado é um {ALUNO/RESPONSÁVEL/PROFESSOR}
  Se responsável: sempre diga 'seu filho {nome}'
  Se professor: sempre diga 'a turma'"
```

**Contexto Estruturado:**
```json
{
  "pergunta": "Quais são minhas tarefas?",
  "intent_data": {
    "intent": "consultar_tarefas",
    "materia": null,
    "periodo": null
  },
  "dados_do_sistema": {
    "intent": "consultar_tarefas",
    "items": [...]
  },
  "user_context": {
    "role": "aluno",
    "user_name": "João",
    "student_name": null
  },
  "contexto_anterior": {
    "intent": "consultar_tarefas",
    "resposta": "Anterior você perguntou..."
  }
}
```

**Generação:**
```
temperature: 0.2       → Mais consistência (determinístico)
maxOutputTokens: 500   → Respostas um pouco mais formadas (~100 palavras)
response_mime_type: application/json  → Para detecção de intenção
```

### 6️⃣ GeminiClient - Conecta com Gemini

Comunicação com a API do Google Gemini.

```php
// 1. Valida se está configurado
if (!$geminiClient->isConfigured()) {
    return $this->answerFormatter->fallback(...);  // Usa fallback
}

// 2. Faz requisição CURL
$response = $geminiClient->generate($payload);

// 3. Trata erros
if (!$response['success']) {
    if ($response['status'] === 429) {  // Rate limit
        return $this->answerFormatter->fallback(...);
    }
    return ['error' => 'Gemini error', 'status' => $response['status']];
}

// 4. Retorna resposta
$texto = $response['data']['candidates'][0]['content']['parts'][0]['text'];
```

**Configuração:**
```env
GEMINI_API_KEY=sua_chave_aqui
GEMINI_MODEL=gemini-2.5-flash
```

### 7️⃣ AnswerFormatter - Formata Resposta

Se Gemini falhar, formata em texto legível:

```php
// Fallback estruturado
if (intent === 'consultar_tarefas') {
    return "Você tem 3 tarefas:\n- Matemática\n- Português\n- História";
}

if (intent === 'consultar_agenda') {
    return "
        Segunda-feira:
        • Matemática (08:00 - 09:00)
        • Português (09:00 - 10:00)
        
        Quarta-feira:
        • Educação Física (14:00 - 15:00)
    ";
}
```

### 8️⃣ ContextManager - Gerencia Estado

Armazena em sessão para follow-ups:

```php
// Salvar após responder
$contextManager->saveConversationContext(
    intent: 'consultar_tarefas',
    data: $tarefas,
    response: "Você tem 3 tarefas..."
);

// Recuperar
$lastIntent = $contextManager->getConversationContext()['last_intent'];
if ($lastIntent === 'consultar_tarefas') {
    // Reutiliza dados anteriores
}
```

---

## 💬 Exemplos de Conversa

### Cenário 1: Aluno Pergunta sobre Tarefas

```
Aluno: "E aí, qual é minha tarefa?"

1. IntentDetector detecta: "consultar_tarefas"
2. AIService busca tarefas do aluno
3. PromptBuilder monta contexto
4. Gemini responde:
   "Você tem 2 tarefas pendentes:
    - Matemática: exercício de álgebra (03/04)
    - Português: redação sobre tecnologia (05/04)"
5. ContextManager salva: last_intent='consultar_tarefas'

---

Aluno: "Qual é o prazo da de Português?"

1. FollowUpDetector detecta "qual" → É follow-up!
2. Reutiliza tarefas já buscadas
3. Gemini responde:
   "A tarefa de Português tem prazo em 05/04, cabe bem dentro de uma semana."
```

### Cenário 2: Responsável Pergunta sobre Filho

```
Responsável: "Como está o desempenho de meu filho?"

1. IntentDetector detecta: "consultar_notas"
2. AIService busca notas de João (filho vinculado)
3. UserContextBuilder adapta para "responsável"
4. PromptBuilder especifica: "seu filho João tem..."
5. Gemini responde:
   "Seu filho João está com bom desempenho.
    Média geral: 8.2
    Disciplinas fortes: Português (9.0), Inglês (8.5)
    Precisa melhorar: Matemática (7.0)"
```

### Cenário 3: Pergunta Fora do Escopo

```
Aluno: "Como funciona relatividade?"

1. IntentDetector detecta: "desconhecido" (não é sobre escola)
2. AnswerFormatter retorna fallback:
   "Desculpe, não consegui entender sua pergunta. Posso ajudá-lo com 
    tarefas, provas, notas, agenda ou frequência."
```

---

## 🔐 Segurança

✅ **Prepared Statements**: Todas queriesusam PDO
✅ **Input Validation**: Pergunta obrigatória, max 500 chars
✅ **Session Checks**: Requer autenticação
✅ **Rate Limiting**: Gemini automático (429)
✅ **Error Handling**: Não expõe stack traces

---

## 📊 Limitações e Fallbacks

| Cenário | Comportamento |
|---------|---------------|
| Gemini indisponível | Usa keywords fallback |
| API rate-limit (429) | Retorna resposta formatada |
| Pergunta vazia | Valida e retorna erro |
| Intenção desconhecida | Retorna feedback útil |
| Follow-up além contexto | Busca novamente os dados |

---

## 🚀 Próximas Melhorias

- [ ] Histórico completo de conversas salvo em DB
- [ ] Análise de sentimentos para alertas
- [ ] Sugestões automáticas baseadas em padrões
- [ ] Integração com calendário para agendamentos
- [ ] Respostas multi-idioma (inglês, espanhol)
- [ ] Fine-tuning do Gemini com dados da escola

