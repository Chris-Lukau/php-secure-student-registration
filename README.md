# php-secure-student-registration
Segurança no form de cadastro de pessoas

# Cadastro Seguro de Alunos (PHP)

Formulário de cadastro com foco em segurança, implementando validação rigorosa de entrada, proteção CSRF, prevenção de XSS e defesa contra SQL Injection.

## 🛡️ Medidas de Segurança Implementadas

### 1. Proteção contra CSRF (Cross-Site Request Forgery)
- Token gerado com `random_bytes(32)` + `bin2hex()`
- Verificação com `hash_equals()` (comparação em tempo constante)
- Cookie de sessão com `SameSite=Lax`

### 2. Prevenção de XSS (Cross-Site Scripting)
- Função `escapar()` usando `htmlspecialchars()` com `ENT_QUOTES | ENT_SUBSTITUTE`
- Escape na saída (não na entrada), aplicado a todos os dados dinâmicos

### 3. Defesa contra SQL Injection
- Uso de **Prepared Statements** (`prepare` + `bind_param`)
- Tipagem explícita: `'si'` (string, integer)

### 4. Validação Rigorosa de Entrada
- Nome: `trim`, verificação UTF-8, bloqueio de caracteres de controle, limite de 100 caracteres
- Idade: `filter_var` com `FILTER_VALIDATE_INT`, faixa 0–120
- Rejeição de tipos inesperados (ex.: array em vez de string)

### 5. Segurança de Sessão
- Cookie com `HttpOnly`, `Secure` (condicional a HTTPS), `SameSite=Lax`
- Mensagem de sucesso armazenada na sessão e removida após exibição (padrão PRG)
- Redirecionamento com HTTP 303 após POST bem-sucedido

## 🚀 Como Executar

1. Clone o repositório
2. Copie `connetion.example.php` para `connetion.php` e configure suas credenciais
3. Crie a tabela:
   ```sql
   CREATE TABLE usuarios (
       id INT AUTO_INCREMENT PRIMARY KEY,
       nome VARCHAR(100) NOT NULL,
       idade TINYINT UNSIGNED NOT NULL
   );


Lição Aprendida

Título: Escapar na saída, não na entrada — e nunca confiar no cliente.

Contexto: Durante o desenvolvimento deste sistema de cadastro, a tentação inicial foi "sanitizar" o nome do usuário logo na entrada (removendo <, >, aspas, etc.) antes de gravar no banco. Parecia mais simples: se o dado entra limpo, tudo que sai está seguro.

O que aprendi: Essa abordagem é frágil por três motivos:

1. Perda de dados legítimos. Um usuário chamado D'Ávila ou Maria <3 teria o nome mutilado no banco para sempre. O dado deve ser armazenado como o usuário digitou — a integridade do dado é responsabilidade do banco, não do formulário.
2. O contexto de saída varia. O mesmo nome pode ser exibido dentro de um atributo HTML (value="..."), dentro de um bloco <script> JavaScript (var n = "..."), dentro de uma URL (href="/perfil?nome=...") ou num header HTTP. Cada contexto exige um escape diferente (htmlspecialchars, json_encode, urlencode, remoção de \r\n). Sanitizar na entrada é escolher um único escape para todos os contextos futuros — e errar em todos menos um.
3. Defesa em profundidade. Se um dia o dado for inserido no banco por outro caminho (importação CSV, API, script de migração), a "sanitização na entrada" não é aplicada — mas o escape na saída continua protegendo.

Por isso a regra que passei a seguir é: validar na entrada (tipo, formato, tamanho, faixa — rejeitar o que é inválido) e escapar na saída (contexto a contexto — tornar seguro o que é válido). São responsabilidades distintas e nenhuma substitui a outra.

Aplicação prática neste projeto: o nome <script>alert(1)</script> é aceito na validação (é uma string UTF-8 válida, dentro do limite de 100 caracteres), é gravado íntegro no banco, e só se torna inofensivo no momento da renderização, quando escapar() o transforma em &lt;script&gt;alert(1)&lt;/script&gt;. Se amanhã esse mesmo dado for exportado para um PDF, um e-mail ou uma API JSON, ele será escapado de novo, com a regra apropriada àquele novo contexto — sem precisar reescrever a camada de entrada.

Conclusão em uma frase: Dados não são "sujos" ou "limpos" em si — são seguros ou perigosos dependendo do contexto em que aparecem; portanto, a defesa pertence à saída, não à entrada.
