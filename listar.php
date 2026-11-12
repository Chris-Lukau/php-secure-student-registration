<?php 
include_once 'connetion.php'; 
// include_once 'processar_lista.php';

# preparar o comando para a consulta no banco
$sql = "SELECT * FROM usuarios";
$result = $conn->query($sql); # armazenar o resultado da consulta

if ($result === false) {
    print "Erro ao consultar!" . $conn -> error;
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de alunos</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style-lista.css">
</head>
<body>
    <main class="container">

        <header class="page-header">
            <div>
                <h1>Lista de alunos</h1>
                <p>
                    <?php 
                        $total = $result->num_rows;
                        echo $total . ($total === 1 ? ' aluno cadastrado' : ' alunos cadastrados');
                    ?>
                </p>
            </div>
            <a href="index.php" class="btn btn-primary top">+ Cadastrar</a>
        </header>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th class="col-id">ID</th>
                        <th>Nome</th>
                        <th>Idade</th>
                        <th class="col-actions">Acções</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="col-id" data-label="ID"><?php echo (int) $row['id']; ?></td>
                                <td data-label="Nome"><?php echo htmlspecialchars($row['nome']); ?></td>
                                <td data-label="Idade"><?php echo (int) $row['idade']; ?></td>
                                <td class="col-actions" data-label="Acções">
                                    <div class="actions">
                                        <a href="editar.php?id=<?php echo (int) $row['id']; ?>" class="btn btn-outline btn-sm">Editar</a>
                                        <form action="deletar.php" method="post">
                                            <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                            <button type="submit" class="btn btn-primary btn-sm"
                                                    onclick="return confirm('Tem certeza que deseja eliminar?')">Deletar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr class="empty-row">
                            <td colspan="4" class="empty">
                                <strong>Nenhum aluno encontrado.</strong>
                                <span>Cadastre o primeiro aluno para ele aparecer aqui.</span>
                                <a href="index.php" class="btn btn-primary">Cadastrar aluno</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>
</body>
</html>