<?php 
  require_once 'connetion.php';

    
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    print "ID Inválido.";
    exit();
}

$stmt = $conn->prepare("SELECT nome, idade FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$aluno = $result->fetch_assoc();
if (!$aluno) {
    echo "<h3 style='background: yellow'>Aluno não encontrado.</h3>";
    exit();
}

// verificar se os dados estão vindo do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // receber dados vindo do formulário
    $nome = trim($_POST['nome']);
    $idade = $_POST['idade'];

    // VALIDAR OS DADOS RECEBIDOS
    if (empty($nome) || empty($idade) || !is_numeric($idade)) {
        echo "<h3 style='background: red'>Dados inválidos</h3>";
        exit;
    }else {
        $idade = (int)$idade; // convertendo idade em inteiro

        // preparando o comando sql para atualizar os dados
        $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, idade = ? WHERE id = ?");
        $stmt->bind_param("sii", $nome, $idade, $id); // vinculando os parâmetros (incluindo o id)


        // executar o comando de sql de inserção
        if ($stmt->execute()) {
          $aluno['nome'] = $nome; // Actualizar o campo nome do formulário
          $aluno['idade'] = $idade; // Actaulizar o campo idade do formulário
            echo "<h3 style='background-color: rgb(116, 26, 47); color: var(--skin);' >Dados Actualizados com Sucesso!</h3>";
            
        }else {
            echo "Erro ao cadastrar!". $stmt->error;
        }

        $stmt->close();

    } 

}

?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edição de aluno</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">

  <style>
    :root {
      /* Paleta da referência */
      --skin: #FFC6A8;        /* Skin Tone */
      --bridal: #741A2F;      /* Bridal */

      /* Derivadas */
      --bridal-dark: #571223;
      --skin-light: #FFE4D6;
      --skin-soft: #FFD6C0;
      --error: #9E1B32;

      --font-display: "Anton", "Impact", "Arial Narrow", sans-serif;
      --font-body: "DM Sans", system-ui, -apple-system, "Segoe UI", sans-serif;
    }

    *, *::before, *::after { box-sizing: border-box; }

    html, body { height: 100%; }

    body {
      margin: 0;
      font-family: var(--font-body);
      color: var(--bridal);
      background: var(--skin);
      -webkit-font-smoothing: antialiased;
    }

    /* ---------- Estrutura: dois blocos de cor, como na referência ---------- */
    .page {
      min-height: 100vh;
      min-height: 100dvh;
      display: grid;
      grid-template-columns: 1fr;
    }

    .intro {
      background: var(--bridal);
      color: var(--skin);
      padding: 2.5rem 1.5rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .intro-inner { width: 100%; max-width: 30rem; margin: 0 auto; }

    .intro h1 {
      margin: 0 0 1rem;
      font-family: var(--font-display);
      font-weight: 400;
      font-size: clamp(3rem, 12vw, 6.5rem);
      line-height: 0.95;
      letter-spacing: 0.01em;
      text-transform: uppercase;
    }

    .intro p {
      margin: 0;
      max-width: 26rem;
      font-size: 1.05rem;
      line-height: 1.6;
      color: var(--skin-soft);
    }

    .panel {
      background: var(--skin);
      padding: 2rem 1.5rem 3rem;
      display: flex;
      align-items: center;
    }

    .form-wrap { width: 100%; max-width: 30rem; margin: 0 auto; }

    /* ---------- Campos ---------- */
    .field { margin-bottom: 1.5rem; }

    label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 700;
      font-size: 0.95rem;
    }

    .hint {
      display: block;
      margin-top: 0.4rem;
      font-size: 0.85rem;
      color: var(--bridal-dark);
      opacity: 0.8;
    }

    input {
      width: 100%;
      padding: 0.9rem 1.1rem;
      font: inherit;
      font-size: 1rem;
      color: var(--bridal-dark);
      background: var(--skin-light);
      border: 2px solid var(--bridal);
      border-radius: 999px;          /* pílula, igual aos campos Hex da referência */
      transition: background-color .15s ease, box-shadow .15s ease;
    }

    input::placeholder { color: var(--bridal); opacity: 0.5; }

    input:hover { background: #FFEBDF; }

    input:focus-visible {
      outline: none;
      background: #fff;
      box-shadow: 0 0 0 4px rgba(116, 26, 47, 0.28);
    }

    input:user-invalid {
      border-color: var(--error);
      background: #FFF1EA;
    }

    /* ---------- Botões ---------- */
    .actions {
      display: flex;
      flex-wrap: wrap;
      gap: 0.75rem;
      margin-top: 2rem;
    }

    .btn {
      flex: 1 1 10rem;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.95rem 1.5rem;
      font: inherit;
      font-size: 1rem;
      font-weight: 700;
      text-decoration: none;
      text-align: center;
      border: 2px solid var(--bridal);
      border-radius: 999px;
      cursor: pointer;
      transition: background-color .15s ease, color .15s ease, transform .1s ease;
    }

    .btn-primary { background: var(--bridal); color: var(--skin); }
    .btn-primary:hover { background: var(--bridal-dark); border-color: var(--bridal-dark); }

    .btn-secondary { background: transparent; color: var(--bridal); }
    .btn-secondary:hover { background: var(--bridal); color: var(--skin); }

    .btn:active { transform: translateY(1px); }

    .btn:focus-visible {
      outline: 3px solid var(--bridal-dark);
      outline-offset: 3px;
    }

    /* ---------- Desktop: lado a lado ---------- */
    @media (min-width: 860px) {
      .page { grid-template-columns: 1fr 1fr; }
      .intro, .panel { padding: 4rem 3rem; }
      .intro-inner { margin-right: 0; }
      .form-wrap { margin-left: 0; }
    }

    @media (prefers-reduced-motion: reduce) {
      * { transition: none !important; }
    }
  </style>
</head>
<body>
  <main class="page">

    <section class="intro">
      <div class="intro-inner">
        <h1>Edição de aluno</h1>
        <p>Preencha os dados abaixo para editar os dados do aluno.</p>
      </div>
    </section>

    <section class="panel">
      <form class="form-wrap" action="" method="post">

        <div class="field">
          <label for="nome">Nome do aluno</label>
          <input type="hidden" id="nome" name="nome" value="<?php echo $id; ?>">
          <input type="text" id="nome" name="nome" placeholder="Ex.: Maria Joana"
                 required autocomplete="name" value="<?php echo $aluno['nome']; ?>">
        </div>

        <div class="field">
          <label for="idade">Idade</label>
          <input type="number" id="idade" name="idade" placeholder="Ex.: 17"
                 min="1" max="120" inputmode="numeric" value="<?php echo $aluno['idade']; ?>" required>
          <span class="hint">Use apenas números.</span>
        </div>


        <div class="actions">
          <button type="submit" class="btn btn-primary">Actualizar</button>
          <a href="listar.php" class="btn btn-secondary">Ver cadastrados</a>
        </div>

      </form>
    </section>

  </main>
</body>
</html>