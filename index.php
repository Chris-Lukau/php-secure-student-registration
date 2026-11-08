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

    <style>
        :root {
            --skin: #FFC6A8;
            --bridal: #741A2F;
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

        /* ---------- Mensagens de feedback ---------- */
        .alert {
            margin: 0 0 1.5rem;
            padding: 1rem 1.25rem;
            border-radius: 1rem;
            font-size: 0.95rem;
            line-height: 1.5;
            border: 2px solid var(--bridal);
            background: var(--skin-light);
        }

        .alert ul { margin: 0; padding-left: 1.15rem; }
        .alert li + li { margin-top: 0.35rem; }

        .alert-success {
            border-color: var(--bridal-dark);
            background: var(--skin-light);
            font-weight: 500;
        }

        .alert-error {
            border-color: var(--error);
            background: #FFF1EA;
        }

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
            border-radius: 999px;
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

        .btn:active { transform: translateY(1px); }

        .btn:focus-visible {
            outline: 3px solid var(--bridal-dark);
            outline-offset: 3px;
        }

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