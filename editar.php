<?php 
  include_once 'connetion.php';

    
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
  <link rel="stylesheet" href="style-editar.css">
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