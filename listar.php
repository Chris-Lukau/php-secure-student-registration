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

    <style>
        :root {
            --skin: #FFC6A8;
            --bridal: #741A2F;
            --bridal-dark: #571223;
            --skin-light: #FFE4D6;
            --skin-soft: #FFD6C0;

            --font-display: "Anton", "Impact", "Arial Narrow", sans-serif;
            --font-body: "DM Sans", system-ui, -apple-system, "Segoe UI", sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: var(--font-body);
            color: var(--bridal);
            background: var(--skin);
            -webkit-font-smoothing: antialiased;
        }

        .container {
            width: 100%;
            max-width: 62rem;
            margin: 0 auto;
            padding: 2.5rem 1.25rem 3rem;
        }

        /* ---------- Cabeçalho: título à esquerda, botão no canto superior direito ---------- */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.75rem;
        }

        .page-header h1 {
            margin: 0;
            font-family: var(--font-display);
            font-weight: 400;
            font-size: clamp(2.25rem, 7vw, 4rem);
            line-height: 1;
            text-transform: uppercase;
        }

        .page-header p {
            margin: 0.5rem 0 0;
            font-size: 0.95rem;
            color: var(--bridal-dark);
            opacity: 0.85;
        }

        /* ---------- Botões ---------- */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.7rem 1.4rem;
            font: inherit;
            font-size: 0.95rem;
            font-weight: 700;
            line-height: 1;
            text-decoration: none;
            white-space: nowrap;
            border: 2px solid var(--bridal);
            border-radius: 999px;
            cursor: pointer;
            transition: background-color .15s ease, color .15s ease, transform .1s ease;
        }

        .btn:active { transform: translateY(1px); }

        .btn:focus-visible {
            outline: 3px solid var(--bridal-dark);
            outline-offset: 3px;
        }

        .btn-primary { background: var(--bridal); color: var(--skin); }
        .btn-primary:hover { background: var(--bridal-dark); border-color: var(--bridal-dark); }

        .btn-outline { background: transparent; color: var(--bridal); }
        .btn-outline:hover { background: var(--bridal); color: var(--skin); }

        .btn-sm { padding: 0.5rem 1rem; font-size: 0.85rem; }

        /* ---------- Tabela ---------- */
        .table-card {
            background: var(--skin-light);
            border: 2px solid var(--bridal);
            border-radius: 1.25rem;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            padding: 1rem 1.25rem;
            text-align: left;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            color: var(--skin);
            background: var(--bridal);
        }

        tbody td {
            padding: 1rem 1.25rem;
            font-size: 0.975rem;
            color: var(--bridal-dark);
            border-top: 1px solid rgba(116, 26, 47, 0.18);
            vertical-align: middle;
        }

        tbody tr { transition: background-color .15s ease; }
        tbody tr:hover { background: var(--skin-soft); }

        .col-id { width: 5rem; font-weight: 700; }
        .col-actions { width: 1%; white-space: nowrap; }
        th.col-actions { text-align: right; }
        td.col-actions { text-align: right; }

        .actions {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .actions form { margin: 0; }

        /* ---------- Estado vazio ---------- */
        .empty {
            padding: 3rem 1.25rem;
            text-align: center;
        }

        .empty strong {
            display: block;
            margin-bottom: 0.35rem;
            font-size: 1.1rem;
        }

        .empty span { display: block; margin-bottom: 1.25rem; opacity: 0.85; }

        tbody tr.empty-row:hover { background: transparent; }

        /* ---------- Mobile: cada linha vira um cartão ---------- */
        @media (max-width: 640px) {
            .container { padding-top: 1.75rem; }

            .btn-primary.top { padding: 0.65rem 1.1rem; }

            thead { display: none; }

            table, tbody, tr, td { display: block; width: 100%; }

            tbody tr { padding: 0.5rem 0; border-top: 1px solid rgba(116, 26, 47, 0.18); }
            tbody tr:first-child { border-top: 0; }

            tbody td {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 0.5rem 1.25rem;
                border-top: 0;
                text-align: right;
            }

            tbody td::before {
                content: attr(data-label);
                font-weight: 700;
                color: var(--bridal);
                text-align: left;
            }

            .col-id, .col-actions { width: 100%; }
            td.col-actions { white-space: normal; }

            td.empty { display: block; }
            td.empty::before { content: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            * { transition: none !important; }
        }
    </style>
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