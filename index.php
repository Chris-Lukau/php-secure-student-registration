<?php
declare(strict_types=1);

$isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
session_set_cookie_params([
    'httponly' => true,
    'secure' => $isHttps,
    'samesite' => 'Lax',
]);
session_start();

require_once __DIR__ . '/connetion.php';

$errors = [];
$nome = '';
$idade = '';
$success = $_SESSION['cadastro_sucesso'] ?? '';
unset($_SESSION['cadastro_sucesso']);

if (
    !isset($_SESSION['csrf_token'])
    || !is_string($_SESSION['csrf_token'])
    || preg_match('/\A[a-f0-9]{64}\z/', $_SESSION['csrf_token']) !== 1
) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? null;

    if (
        !is_string($submittedToken)
        || !hash_equals($_SESSION['csrf_token'], $submittedToken)
    ) {
        $errors[] = 'A sessão do formulário expirou. Atualize a página e tente novamente.';
    } else {
        $nomeInput = $_POST['nome'] ?? null;
        $idadeInput = $_POST['idade'] ?? null;

        if (!is_string($nomeInput)) {
            $errors[] = 'Informe um nome válido.';
        } else {
            $nome = trim($nomeInput);

            if (
                $nome === ''
                || preg_match('//u', $nome) !== 1
                || preg_match('/[\x00-\x1F\x7F]/', $nome) === 1
                || preg_match('/\A.{1,100}\z/us', $nome) !== 1
            ) {
                $errors[] = 'O nome é obrigatório e deve ter até 100 caracteres válidos.';
            }
        }

        if (!is_string($idadeInput)) {
            $errors[] = 'Informe uma idade válida.';
        } else {
            $idade = $idadeInput;
            $idadeValidada = filter_var(
                $idadeInput,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 0, 'max_range' => 120]]
            );

            if ($idadeValidada === false) {
                $errors[] = 'A idade deve ser um número inteiro entre 0 e 120.';
            }
        }
    }

    if ($errors === []) {
        try {
            $stmt = $conn->prepare(
                'INSERT INTO usuarios (nome, idade) VALUES (?, ?)'
            );
            $stmt->bind_param('si', $nome, $idadeValidada);
            $stmt->execute();
            $stmt->close();

            $_SESSION['cadastro_sucesso'] = 'Cadastro realizado com sucesso.';
            header('Location: listar.php', true, 303);
            exit;
        } catch (mysqli_sql_exception $exception) {
            error_log('Erro ao cadastrar usuário: ' . $exception->getMessage());
            $errors[] = 'Não foi possível concluir o cadastro. Tente novamente mais tarde.';
        }
    }
}

function escapar(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de aluno</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style-index.css">
</head>
<body>
    <main class="page">

        <section class="intro">
            <div class="intro-inner">
                <h1>Cadastro de aluno</h1>
                <p>Preencha os dados abaixo para adicionar um novo aluno à lista.</p>
            </div>
        </section>

        <section class="panel">
            <form class="form-wrap" method="post" action="">

                <?php if ($success !== ''): ?>
                    <div class="alert alert-success" role="status">
                        <?= escapar((string) $success) ?>
                    </div>
                <?php endif; ?>

                <?php if ($errors !== []): ?>
                    <div class="alert alert-error" role="alert">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= escapar($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= escapar($_SESSION['csrf_token']) ?>"
                >

                <div class="field">
                    <label for="nome">Nome do aluno</label>
                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        placeholder="Ex.: Maria Joana"
                        maxlength="100"
                        autocomplete="name"
                        value="<?= escapar($nome) ?>"
                        required
                    >
                </div>

                <div class="field">
                    <label for="idade">Idade</label>
                    <input
                        type="number"
                        id="idade"
                        name="idade"
                        placeholder="Ex.: 17"
                        min="0"
                        max="120"
                        step="1"
                        inputmode="numeric"
                        value="<?= escapar($idade) ?>"
                        required
                    >
                    <span class="hint">Use apenas números.</span>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary">Cadastrar</button>
                </div>

            </form>
        </section>

    </main>
</body>
</html>